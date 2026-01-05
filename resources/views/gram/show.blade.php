@extends('layouts.app')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="card mb-5 mb-xl-8">
        <div class="py-2">
            <div class="card-body pt-2">
                <div class="card-body pt-0 pb-5">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                            <!--begin::Table body-->
                            <tbody class="fs-6 text-gray-600">
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.gram.fields.name') }}</b></td>
                                    <td>{{ $gram->name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.gram.fields.gramjuth') }}</b></td>
                                    <td>{{ $gram->gramjuth ? $gram->gramjuth->name : '' }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.gram.fields.status') }}</b></td>
                                    <td>@if ($gram->status == '1')
                                        Active
                                        @else
                                        Inactive
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer border-0 d-flex justify-content-center pt-0">
            <a class="btn btn-xs btn-primary" href="{{ route('grams.index') }}">{{ trans('messages.gram.fields.back') }}</a>
        </div>
    </div>
</div>
@endsection
