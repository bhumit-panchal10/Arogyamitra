@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::open(['method' => 'POST', 'route' => ['password.change'], 'class' => 'form','id'=>'frmUser', 'autocomplete' => 'off']) !!}

                <div class="d-flex flex-column mb-2 fv-row passwordField">
                    {!! htmlspecialchars_decode(Form::label('old Password', 'Old Password <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::password('current_password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'autocomplete' => 'off', 'placeholder' => 'Enter Old Password']) !!}
                    @if($errors->has('current_password'))
                    <p class="help-block text-danger">
                        {{ $errors->first('current_password') }}
                    </p>
                    @endif
                </div>
                <div class="d-flex flex-column mb-2 fv-row passwordField">
                    {!! htmlspecialchars_decode(Form::label('Change password', 'New Password <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::password('new_password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'autocomplete' => 'off', 'placeholder' => 'Enter New Password']) !!}
                    @if($errors->has('new_password'))
                    <p class="help-block text-danger">
                        {{ $errors->first('new_password') }}
                    </p>
                    @endif
                </div>
                <div class="d-flex flex-column mb-2 fv-row passwordField">
                    {!! htmlspecialchars_decode(Form::label('Confirm password', 'Confirm Password <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::password('confirm_password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'autocomplete' => 'off', 'placeholder' => 'Enter Confirm Password']) !!}
                    @if($errors->has('confirm_password'))
                    <p class="help-block text-danger">
                        {{ $errors->first('confirm_password') }}
                    </p>
                    @endif
                </div>
                @php
                $save= trans('messages.users.fields.save')
                @endphp
                {!! Form::submit('Save', ['class' => 'btn btn-success mt-3']) !!}
                <a href="{{route('home')}}" class="btn btn-danger mt-3">{{ trans('messages.users.fields.cancel') }}</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script>
    $(document).ready(function() {
        var role = $('#role').val();
        if (role == '1') {
            $('.passwordField').removeClass('d-none');
        }

        if (role == '5') {
            $('.passwordField').removeClass('d-none');
        }

        if (role == '4') {
            $('.passwordField').removeClass('d-none');
        }

        if (role == '2') {
            $('.passwordField').removeClass('d-none');
        }

    });
</script>
@endsection