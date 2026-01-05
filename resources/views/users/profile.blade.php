@extends('layouts.app')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-2">
            <div class="py-2">
                <div class="card-header border-0">
                    <div class="card-title">
                        <h2> {{ trans('messages.users.name') }} {{ $user->name }}</h2>
                    </div>
                </div>
                <div class="card-body pt-0 pb-5">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                            <!--begin::Table body-->
                            <tbody class="fs-6 fw-bold text-gray-600">

                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.name') }}</b></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.email') }}</b></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <!-- <tr>
                                    <td>{{ trans('messages.users.fields.username') }}</td>
                                    <td>{{ $user->username }}</td>
                                </tr> -->
                                @if($user->role == 1)
                                @php
                                $role='Backend'
                                @endphp
                                @elseif ($user->role == 2)
                                @php
                                $role='App User'
                                @endphp
                                @elseif ($user->role == 4)
                                @php
                                $role='Vibhag User'
                                @endphp
                                @elseif ($user->role == 5)
                                @php
                                $role='Prant User'
                                @endphp
                                @else
                                @php
                                $role='Arogya Mitra'
                                @endphp
                                @endif
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.role') }}</b></td>
                                    <td>{{ $role }}</td>
                                </tr>
                                @if($user->role == '4')
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.vibhag') }}</b></td>
                                    <td>{{ $user->v_name }}</td>
                                </tr>
                                @endif
                                @if($user->role == '5')
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.prant') }}</b></td>
                                    <td>{{ $user->p_name }}</td>
                                </tr>
                                @endif
                                @if($user->address)
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.address') }}</b></td>
                                    <td>{!! nl2br($user->address) !!}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.mobile_no') }}</b></td>
                                    <td>{{ $user->mobile_no }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.status') }}</b></td>
                                    <td>{{ $user->status }}</td>
                                </tr>


                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer border-0 d-flex justify-content-center pt-0">
            <a class="btn btn-xs btn-primary" href="{{ route('home') }}">{{ trans('messages.users.fields.return') }}</a>
        </div>
    </div>
</div>
@endsection