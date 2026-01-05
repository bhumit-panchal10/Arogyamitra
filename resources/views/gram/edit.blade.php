@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::open(['route' => ['grams.update', $gram->id], 'method' => 'PUT']) !!}
                @csrf
                <input type="hidden" name="gram_id" value="{{ $gram->id }}" />
                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', $gram->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name', 'maxlength' => 20, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z0-9 \\u0A80-\\u0AFF]/g, '')"]) !!}

                    @if($errors->has('name'))
                    <p class="help-block text-danger ">
                        {{ $errors->first('name') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('gramjuth_id', 'Gramjuth <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::select('gramjuth_id', [$gram->gramjuth_id => $gram->gramjuth_name] + $grams->pluck('gramjuth_name', 'gramjuth_id')->toArray(), $gram->gramjuth_id, ['class' => 'form-control' . ($errors->has('gramjuth_id') ? ' is-invalid' : ''), 'id' => 'gramjuth_id','data-control'=>"select2", 'placeholder' => 'Select gramjuth']) !!}
                    @if($errors->has('gramjuth_id'))
                    <p class="help-block text-danger ">
                        {{ $errors->first('gramjuth_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span> ', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '1', $gram->status == 1, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! Form::label('status1', 'Active', ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '0', $gram->status == 0, ['class' => 'form-check-input', 'id' => 'status0']) !!}
                            {!! Form::label('status0', 'Inactive', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
        <a href="{{route('grams.index')}}" class="btn btn-danger">{{ trans('messages.gram.fields.cancel') }}</a>
        {!! Form::close() !!}
    </div>
</div>
@stop