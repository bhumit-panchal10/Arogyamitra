@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::model($jilla, ['method' => 'PUT', 'route' => ['jilla.update', $jilla->id]]) !!}
                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!} {!! Form::text('name', $jilla->jilla_name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name', 'maxlength' => 20, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z \\u0A80-\\u0AFF]/g, '')"]) !!}
                    @error('name')
                    <div class="invalid-feedback" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                {!! htmlspecialchars_decode(Form::label('vibhag', 'Vibhag <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('vibhag_id', $vibhag, $jilla->vibhag->id, array('data-control'=> 'select2', 'class'=>'form-control' . ($errors->has('vibhag_id') ? ' is-invalid' : ''),'placeholder'=>'Select vibhag')) }}
                    @error('vibhag_id')
                    <div class="invalid-feedback" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '1', $jilla->status, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! Form::label('status1', 'Active', ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '0', $jilla->status, ['class' => 'form-check-input', 'id' => 'status0']) !!}
                            {!! Form::label('status0', 'Inactive', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
                {!! Form::button('Cancel', ['class' => 'btn btn-danger', 'onclick' => 'window.location.href="' . route('jilla.index') . '"']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop