@extends('layouts.app')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="card mb-5 mb-xl-8">
        <div class="py-2">
            <div class="card-body pt-0 pb-5">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                        <!--begin::Table body-->
                        <tbody class="fs-6 fw-bold text-gray-600">
                            <tr>
                                <td><b class="text-black">{{ trans('messages.jilla.fields.name') }}</b></td>
                                <td>{{ $jilla->jilla_name }}</td>
                            </tr>
                            <tr>
                                <td><b class="text-black">{{ trans('messages.jilla.fields.vibhag') }}</b></td>
                                <td>{{ $jilla->vibhag_name }}</td>
                            </tr>
                            <tr>
                                <td><b class="text-black">{{ trans('messages.jilla.fields.status') }}</b></td>
                                <td>{{ $jilla->status == 1 ? 'Active' : 'Inactive' }}</td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                </div>
            </div>

        </div>
        <div class="card-footer border-0 d-flex justify-content-center pt-0">
            <a class="btn btn-xs btn-primary" href="{{ route('jilla.index') }}">{{ trans('messages.jilla.fields.back') }}</a>
        </div>
    </div>
</div>
@endsection