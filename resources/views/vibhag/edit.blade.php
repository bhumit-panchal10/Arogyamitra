@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::model($vibhag, ['method' => 'PUT', 'route' => ['vibhag.update', $vibhag->id]]) !!}

                <div class="d-flex flex-column mb-3 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name', 'maxlength' => 20, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z \\u0A80-\\u0AFF]/g, '')"]) !!}
                    @error('name')
                    <div class="invalid-feedback" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('prant', 'Prant <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('prant_id', $prant, null, array('data-control'=> 'select2', 'class'=>'form-control' . ($errors->has('prant_id') ? ' is-invalid' : ''),'placeholder'=>'Select Prant')) }}
                    @error('prant_id')
                    <div class="invalid-feedback" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '1', true, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! Form::label('status1', 'Active', ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '0', false, ['class' => 'form-check-input', 'id' => 'status0']) !!}
                            {!! Form::label('status0', 'Inactive', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>

                {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
                <a href="{{route('vibhag.index')}}" class="btn btn-danger">{{ trans('messages.vibhag.fields.cancel') }}</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop