@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::model($taluka, ['method' => 'PUT', 'route' => ['taluka.update', $taluka->id]]) !!}
                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', $taluka->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name', 'maxlength' => 20, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z \\u0A80-\\u0AFF]/g, '')"]) !!}
                    @if($errors->has('name'))
                    <p class="help-block text-danger ">
                        {{ $errors->first('name') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('jilla_id', 'Jilla <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::select('jilla_id', $jilla, $taluka->jilla->id, ['data-control' => 'select2', 'class' => 'form-control' . ($errors->has('jilla_id') ? ' is-invalid' : ''), 'id' => 'jilla_id', 'placeholder' => 'Select jilla']) !!}
                    @if($errors->has('jilla_id'))
                    <p class="help-block text-danger ">
                        {{ $errors->first('jilla_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-4 fv-row ">
                {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '1', $taluka->status, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! Form::label('status1', 'Active', ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '0', $taluka->status, ['class' => 'form-check-input', 'id' => 'status0']) !!}
                            {!! Form::label('status0', 'Inactive', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>

                {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
                {!! Form::button('Cancel', ['class' => 'btn btn-danger', 'onclick' => 'window.location.href="' . route('taluka.index') . '"']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop