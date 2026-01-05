@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::open(['method' => 'POST', 'route' => ['jilla.store'], 'class' => 'form','autocomplete' => 'off']) !!}
                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Enter Name', 'maxlength' => 20, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z \\u0A80-\\u0AFF]/g, '')"]) !!}
                    @error('name')
                    <div class="invalid-feedback" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Prant <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('prant_id', $prant, null, array('data-control' => 'select2','class' => 'form-control' . ($errors->has('prant_id') ? ' is-invalid' : ''), 'id'=>'prant_id','placeholder'=>'Select Prant','onchange'=>"getVibhagList(this.value)")) }}
                    @if($errors->has('prant_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('prant_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Vibhag <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('vibhag_id', ['' => 'Select vibhag'], null, ['class' => 'form-control' . ($errors->has('vibhag_id') ? ' is-invalid' : ''), 'id' => 'vibhag_id', 'data-control' => 'select2']) }}
                    @if($errors->has('vibhag_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('vibhag_id') }}
                    </p>
                    @endif
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

                @php
                $save= trans('messages.jilla.fields.save')
                @endphp
                {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
                {!! Form::button('Cancel', ['class' => 'btn btn-danger', 'onclick' => 'window.location.href="' . route('jilla.index') . '"']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    function getVibhagList(id) {
        $.ajax({
            url: "{{ url('vibhag-list') }}",
            type: 'GET',
            data: {
                prantId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Vibhag of " + $('#prant_id').find(':selected').text() + "</option>";
                if (response) {
                    $.each(response, function(key, val) {
                        html += "<option value='" + val.id + "'>" + val.name + "</option>";
                    });
                    $('#vibhag_id').html(html);
                } else {
                    $('#vibhag_id').html('');
                }
            }
        });
    }
</script>
@endsection