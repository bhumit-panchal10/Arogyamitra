@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">

                {!! Form::open(['route' => ['medicines.update', $medicine->id], 'method' => 'PUT']) !!}
                <div class="d-flex flex-column mb-3 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', $medicine->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name', 'maxlength' => 50, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z0-9 \\u0A80-\\u0AFF]/g, '')"]) !!}
                    @error('name')
                    <div class="invalid-feedback" style="color: red;">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-3 fv-row">
                    {!! htmlspecialchars_decode(Form::label('qty', 'Quantity <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::number('qty', $medicine->qty, ['class' => 'form-control '.($errors->has('qty') ? 'is-invalid' : ''), 'id' => 'quantity', 'min' => '1', 'placeholder' => 'Quantity', 'maxlength' => '4', 'pattern' => '\d*']) !!}

                    @error('qty')
                    <div class="invalid-feedback" style="color: red;">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-3 fv-row">
                    {!! htmlspecialchars_decode(Form::label('qty_type', 'Quantity Type <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::select('qty_type', ['' => 'Select Quantity type', 'નંગ' => 'Pcs(નંગ)', 'ગ્રામ' => 'Grm(ગ્રામ)', 'મી.લી.' => 'Ml(મી.લી.)'], $medicine->qty_type, ['data-control'=> 'select2', 'class' => 'form-control '.($errors->has('qty_type') ? 'is-invalid' : ''), 'id' => 'qty_type']) !!}
                    @error('qty_type')
                    <div class="invalid-feedback" style="color: red;">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-4 fv-row ">
                    {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '1', $medicine->status == 1, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! Form::label('status1', 'Active', ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '0', $medicine->status == 0, ['class' => 'form-check-input', 'id' => 'status0']) !!}
                            {!! Form::label('status0', 'Inactive', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>

                {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
                {!! Form::button('Cancel', ['class' => 'btn btn-danger', 'onclick' => 'window.location.href="' . route('medicines.index') . '"']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection