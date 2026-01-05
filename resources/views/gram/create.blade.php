@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::open(['route' => 'grams.store', 'method' => 'POST', 'class' => 'form', 'autocomplete' => 'off']) !!}
                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Enter Name', 'maxlength' => 20, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z0-9 \\u0A80-\\u0AFF]/g, '')"]) !!}
                    @if($errors->has('name'))
                    <p class="help-block text-danger ">
                        {{ $errors->first('name') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Prant <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('prant', $prant, null, array('data-control' => 'select2','class' => 'form-control' . ($errors->has('prant') ? ' is-invalid' : ''), 'id'=>'prant_id','placeholder'=>'Select Prant','onchange'=>"getVibhagList(this.value)")) }}
                    @if($errors->has('prant'))
                    <p class="help-block text-danger">
                        {{ $errors->first('prant') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Vibhag <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('vibhag_id', ['' => 'Select Vibhag'], null, ['class' => 'form-control' . ($errors->has('vibhag_id') ? ' is-invalid' : ''), 'id' => 'vibhag_id', 'data-control' => 'select2', 'onchange'=>"getJillaList(this.value)"]) }}
                    @if($errors->has('vibhag_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('vibhag_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('jilla', 'Jilla <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <select class="{{ $errors->has('jilla_id') ? 'form-control is-invalid' : 'form-control' }}" id="jilla" data-control="select2" onchange="getTalukaList(this.value)" name="jilla_id">
                        <option value="">Select Jilla</option>
                    </select>
                    @if($errors->has('jilla_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('jilla_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('taluka', 'Taluka <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <select class="{{ $errors->has('taluka_id') ? 'form-control is-invalid' : 'form-control' }}" id="taluka" data-control="select2" onchange="getGramjuthList(this.value)" name="taluka_id">
                        <option value="">Select Taluka</option>
                    </select>
                    @if($errors->has('taluka_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('taluka_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('gramjuth', 'Gramjuth <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <select class="{{ $errors->has('gramjuth_id') ? 'form-control is-invalid' : 'form-control' }}" id="gramjuth" data-control="select2" onchange="getGramList(this.value)" name="gramjuth_id">
                        <option value="">Select Gramjuth</option>
                    </select>
                    @if($errors->has('gramjuth_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('gramjuth_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span> ', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '1', true, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! htmlspecialchars_decode(Form::label('status1', 'Active', ['class' => 'form-check-label'])) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '0', false, ['class' => 'form-check-input', 'id' => 'status0']) !!}
                            {!! htmlspecialchars_decode(Form::label('status0', 'Inactive', ['class' => 'form-check-label'])) !!}
                        </div>
                    </div>
                </div>

                @php
                $save= trans('messages.gram.fields.save')
                @endphp
                {!! Form::submit('Save', ['class' => 'btn btn-success mt-3']) !!}
                <a href="{{route('grams.index')}}" class="btn btn-danger mt-3">Cancel</a>
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

    function getJillaList(id) {
        $.ajax({
            url: "{{ url('jilla-list') }}",
            type: 'GET',
            data: {
                vibhagId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Jilla of " + $('#vibhag_id').find(':selected').text() + "</option>";
                if (response) {
                    $.each(response, function(key, val) {
                        html += "<option value='" + val.id + "'>" + val.name + "</option>";
                    });
                    $('#jilla').html(html);
                } else {
                    $('#jilla').html('');
                }
            }
        });
    }

    function getTalukaList(id) {
        $.ajax({
            url: "{{ url('taluka-list') }}",
            type: 'GET',
            data: {
                jillaId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Jilla of " + $('#jilla').find(':selected').text() + "</option>";
                if (response) {
                    $.each(response, function(key, val) {
                        html += "<option value='" + val.id + "'>" + val.name + "</option>";
                    });
                    $('#taluka').html(html);
                } else {
                    $('#taluka').html('');
                }
                $('#taluka,#gramjuth,#gram').removeAttr('selected').find('option:first').attr('selected', 'selected');
            }
        });
    }

    function getGramjuthList(id) {
        $.ajax({
            url: "{{ url('gramjuth-list') }}",
            type: 'GET',
            data: {
                talukaId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Jilla of " + $('#taluka').find(':selected').text() + "</option>";
                if (response) {
                    $.each(response, function(key, val) {
                        html += "<option value='" + val.id + "'>" + val.name + "</option>";
                    });
                    $('#gramjuth').html(html);
                } else {
                    $('#gramjuth').html(html);
                }
            }
        });
    }

    function getGramList(id) {
        $.ajax({
            url: "{{ url('gram-list') }}",
            type: 'GET',
            data: {
                gramjuthId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''>Select Gam</option>";
                if (response) {
                    $.each(response, function(key, val) {
                        html += "<option value='" + val.id + "'>" + val.name + "</option>";
                    });
                    $('#gram').html(html);
                } else {
                    $('#gram').html('');
                }
            }
        });
    }
</script>
@endsection