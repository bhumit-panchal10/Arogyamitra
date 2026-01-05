@extends('layouts.app')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-2">
            <div class="py-2">
                <div class="card-body pt-0 pb-5">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                            <!--begin::Table body-->
                            <tbody class="fs-6 fw-bold text-gray-600">
                                @if($user->role == '1')
                                @php
                                $role='Backend'
                                @endphp
                                @elseif ($user->role == 2)
                                @php
                                $role='App User'
                                @endphp
                                @elseif ($user->role == 3)
                                @php
                                $role='Arogya Mitra'
                                @endphp
                                @elseif ($user->role == 4)
                                @php
                                $role='Vibhag User'
                                @endphp
                                @elseif ($user->role == 6)
                                @php
                                $role='Stockiest User'
                                @endphp
                                @else
                                @php
                                $role='Prant User'
                                @endphp
                                @endif
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.name') }}</b></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.role') }}</b></td>
                                    <td>{{ $role }}</td>
                                </tr>
                                @if($user->address)
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.address') }}</b></td>
                                    <td>{!! nl2br($user->address) !!}</td>
                                </tr>
                                @endif
                                @if($user->role == '1')
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.email') }}</b></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.mobile_no') }}</b></td>
                                    <td>{{ $user->mobile_no }}</td>
                                </tr>
                                @elseif($user->role == '2' OR $user->role == '6')
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.prant') }}</b></td>
                                    <td>{{ $user->p_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.vibhag') }}</b></td>
                                    <td>{{ $user->vi_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.jilla') }}</b></td>
                                    <td>{{ $user->jila_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.alloted_gram') }}</b></td>
                                    @if($gramArray)
                                        @php
                                        $chunks = array_chunk($gramArray, ceil(count($gramArray) / 2));
                                        @endphp
                                        <td>
                                            @foreach ($chunks[0] as $value)
                                            <ul>
                                                <li>{{ $value['name'] }} </li>
                                            </ul>
                                            @endforeach
                                        </td>
                                        @if(isset($chunks[1]))
                                        <td>
                                            @foreach ($chunks[1] as $value)
                                            <ul>
                                                <li>{{ $value['name'] }} </li>
                                            </ul>
                                            @endforeach
                                        </td>
                                        @endif
                                    @else
                                    <td>Not Alloted</td>
                                    @endif
                                </tr>
                                @elseif($user->role == '3')
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.mobile_no') }}</b></td>
                                    <td>{{ $user->mobile_no }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.prant') }}</b></td>
                                    <td>{{ $user->p_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.vibhag') }}</b></td>
                                    <td>{{ $user->vi_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.jilla') }}</b></td>
                                    <td>{{ $user->jila_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.taluka') }}</b></td>
                                    <td>{{ $user->tk_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.gramjuth') }}</b></td>
                                    <td>{{ $user->grm_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.gram') }}</b></td>
                                    <td>{{ $user->gam_name }}</td>
                                </tr>
                                @elseif($user->role == '4')
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.prant') }}</b></td>
                                    <td>{{ $user->p_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.vibhag') }}</b></td>
                                    <td>{{ $user->v_name }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.email') }}</b></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                @elseif($user->role == '5')
                                <tr>
                                    <td><b class="text-black">{{ trans('messages.users.fields.prant') }}</b></td>
                                    <td>{{ $user->p_name }}</td>
                                </tr>
                                @endif
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
            <a class="btn btn-xs btn-primary" href="{{ route('users.index') }}">{{ trans('messages.users.fields.back') }}</a>
        </div>
    </div>
</div>
@endsection