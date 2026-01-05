@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::open(['method' => 'POST', 'route' => ['users.store'], 'class' => 'form','id'=>'frmUser', 'autocomplete' => 'off']) !!}

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Enter Name', 'maxlength' => 50, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z \\u0A80-\\u0AFF]/g, '')", 'data-error' => '#errNm1']) !!}

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm1"></span>
                    </div>

                    @if($errors->has('name'))
                    <p class="help-block text-danger">
                        {{ $errors->first('name') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('role', 'Role <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('role', $role, null, ['data-control' => 'select2', 'onchange' => 'checkRole(this.value)', 'class' => 'form-control' . ($errors->has('role') ? ' is-invalid' : ''), 'id' => 'role', 'placeholder' => 'Select Role', 'data-error' => '#errNm2']) }}
                    <span class="messages"></span>

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm2"></span>
                    </div>
                    @if($errors->has('role'))
                    <p class="help-block text-danger ">
                        {{ $errors->first('role') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row emailField {{ $display}}">
                    {!! htmlspecialchars_decode(Form::label('email', 'Email <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('email', old('email'), ['class' => 'form-control'. ($errors->has('email') ? ' is-invalid' : ''),'onkeyup'=>'CheckEmail(this.value)', 'placeholder' => 'Enter Email', 'data-error' => '#errNm3']) !!}
                    <span class="checkEmail"></span>

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm3"></span>
                    </div>
                    @if($errors->has('email'))
                    <p class="help-block text-danger ">
                        {{ $errors->first('email') }}
                    </p>
                    @endif

                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('mobile_no', 'Mobile No <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::text('mobile_no', null, ['class' => 'form-control' . ($errors->has('mobile_no') ? ' is-invalid' : ''), 'placeholder' => 'Enter Mobile No.', 'pattern' => '^(\+\d{1,3}[- ]?)?\d{10}$', 'maxlength' => '10', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'onkeyup' => 'CheckMobile(this.value)', 'autocomplete' => 'off', 'data-error' => '#errNm4']) }}
                    <span class="checkMobile"></span>

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm4"></span>
                    </div>
                    @if($errors->has('mobile_no'))
                    <p class="help-block text-danger">
                        {{ $errors->first('mobile_no') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! Form::label('address', 'Address', ['class' => 'control-label fs-5 fw-bold mb-2']) !!}
                    {!! Form::textarea('address',null,['class' => 'form-control', 'placeholder'=>'Enter Address', 'rows' => 2, 'cols' => 40]) !!}
                </div>

                <div class="d-flex flex-column mb-2 fv-row passwordField">
                    {!! htmlspecialchars_decode(Form::label('password', 'Password <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::password('password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'autocomplete' => 'off', 'placeholder' => 'Enter Password', 'data-error' => '#errNm5']) !!}

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm5"></span>
                    </div>
                    @if($errors->has('password'))
                    <p class="help-block text-danger">
                        {{ $errors->first('password') }}
                    </p>
                    @endif
                </div>

                @if(Auth::user()->role == 4 || Auth::user()->role == 5)
                    {{ Form::hidden('prant_id', Auth::user()->prant_id) }}
                @else
                    <div class="d-flex flex-column mb-4 fv-row prantField {{ $display}}">
                        {!! htmlspecialchars_decode(Form::label('prant', 'Prant <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                        {{ Form::select('prant_id', $prant, null, ['data-control'=> 'select2', 'id' => 'prant', 'class'=>'form-control prant' . ($errors->has('prant_id') ? ' is-invalid' : ''), 'onchange'=> 'getVibhagList(this.value)', 'placeholder'=>'Select Prant', 'data-error' => '#errNm6']) }}

                        <div class="help-block text-danger errorTxt">
                            <span id="errNm6"></span>
                        </div>

                        @if($errors->has('prant_id'))
                            <p class="help-block text-danger">
                                {{ $errors->first('prant_id') }}
                            </p>
                        @endif
                    </div>
                @endif

                <div class="d-flex flex-column mb-2 fv-row vibhagField {{ $display}}">
                    {!! htmlspecialchars_decode(Form::label('vibhag', 'Vibhag <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('vibhag_id', $vibhags, null, ['data-control'=> 'select2', 'id' => 'vibhag', 'class'=>'form-control vibhag' . ($errors->has('vibhag_id') ? ' is-invalid' : ''), 'onchange'=> 'getJillaList(this.value)', 'placeholder'=>'Select Vibhag', 'data-error' => '#errNm7']) }}

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm7"></span>
                    </div>
                    @if($errors->has('vibhag_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('vibhag_id') }}
                    </p>
                    @endif
                </div>

                @if(Auth::user()->role == 4)
                    {{ Form::hidden('prant_id', Auth::user()->prant_id) }}
                    {{ Form::hidden('vibhag_id', Auth::user()->vibhag_id) }}
                    <div class="d-flex flex-column mb-2 fv-row jillaField">
                        {!! htmlspecialchars_decode(Form::label('name', 'Jilla <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                        {{ Form::select('jilla_id', $jillaField, null, ['class' => 'form-control' . ($errors->has('jilla_id') ? ' is-invalid' : ''), 'id' => 'jilla', 'data-control' => 'select2', 'onchange'=>"getTalukaList(this.value)", 'data-error' => '#errNm8']) }}

                        <div class="help-block text-danger errorTxt">
                            <span id="errNm8"></span>
                        </div>
                        @if($errors->has('vibhag_id'))
                        <p class="help-block text-danger">
                            {{ $errors->first('vibhag_id') }}
                        </p>
                        @endif
                    </div>
                @else
                    <div class="d-flex flex-column mb-2 fv-row JillaField {{ $display}}">
                        {!! htmlspecialchars_decode(Form::label('jilla', 'Jilla <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                        {{ Form::select('jilla_id', $jilla, null, ['data-control'=> 'select2', 'id' => 'jilla', 'class'=>'form-control jilla' . ($errors->has('jilla_id') ? ' is-invalid' : ''), 'onchange'=> 'getTalukaList(this.value)', 'placeholder'=>'Select Jilla', 'data-error' => '#errNm8']) }}

                        <div class="help-block text-danger errorTxt">
                            <span id="errNm8"></span>
                        </div>
                        @if($errors->has('jilla_id'))
                            <p class="help-block text-danger">
                                {{ $errors->first('jilla_id') }}
                            </p>
                        @endif
                    </div>
                @endif

                <div class="d-flex flex-column mb-2 fv-row talukaField {{ $display}}">
                    {!! htmlspecialchars_decode(Form::label('taluka', 'Taluka <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('taluka_id', $taluka, null, ['data-control'=> 'select2', 'id' => 'taluka', 'class'=>'form-control taluka' . ($errors->has('taluka_id') ? ' is-invalid' : ''), 'onchange'=> 'getGramjuthList(this.value)', 'placeholder'=>'Select Taluka', 'data-error' => '#errNm9']) }}

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm9"></span>
                    </div>
                    @if($errors->has('taluka_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('taluka_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row gramjuth_idField {{ $display}}">
                    {!! htmlspecialchars_decode(Form::label('gramjuth_id', 'Gramjuth <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('gramjuth_id', $gramjuth, null, ['class' => $errors->has('gramjuth_id') ? 'form-control is-invalid' : 'form-control', 'id' => 'gramjuth', 'data-control' => 'select2', 'onchange' => 'getGramList(this.value)',  'placeholder' => 'Select Gramjuth', 'data-error' => '#errNm10']) }}

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm10"></span>
                    </div>
                    @if($errors->has('gramjuth_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('gramjuth_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row gramField {{ $display}}">
                    {!! htmlspecialchars_decode(Form::label('gram_id', 'Gram <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {{ Form::select('gram_id', $gram, null, ['class' => $errors->has('gram_id') ? 'form-control is-invalid' : 'form-control', 'id' => 'gram', 'data-control' => 'select2', 'placeholder' => 'Select Gram', 'data-error' => '#errNm11']) }}

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm11"></span>
                    </div>
                    @if($errors->has('gram_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('gram_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row gramJillaField {{ $display}}">
                    {!! htmlspecialchars_decode(Form::label('gram', 'Gram <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="d-flex fw-bold h-100">
                        <div class="form-check form-check-custom form-check-solid me-2">
                            <input class="form-check-input" id="selectAll" data-error="#errNm12" type="checkbox" name="gramId[]" value="" />
                            <label class="form-check-label mt-2">Select All</label>
                        </div>
                    </div>

                    <div class="col-xl-9" id="gramList">
                    </div>

                    <div class="help-block text-danger errorTxt">
                        <span id="errNm12"></span>
                    </div>
                    @if($errors->has('gram_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('gram_id') }}
                    </p>
                    @endif
                </div>

                <div class="d-flex flex-column mb-2 fv-row">
                    {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span> ', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', 'Active', true, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! htmlspecialchars_decode(Form::label('status1', 'Active', ['class' => 'form-check-label'])) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', 'Deactive', false, ['class' => 'form-check-input', 'id' => 'status0']) !!}
                            {!! htmlspecialchars_decode(Form::label('status0', 'Inactive', ['class' => 'form-check-label'])) !!}
                        </div>
                    </div>
                </div>

                @php
                $save = trans('messages.users.fields.save')
                @endphp
                {!! Form::submit('Save', ['class' => 'btn btn-success mt-3 user']) !!}
                    <a href="{{ route('users.index') }}" class="btn btn-danger mt-3">{{ trans('messages.users.fields.cancel') }}</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script src="{{ url('/assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ url('/assets/js/user-form-validation.js') }}"></script>
<script>
    $(document).ready(function() {
        var role = $('#role').val();
        var prant = $('#prant').val();
        var vibhag = $('#vibhag').val();
        var jilla = $('#jilla').val();
        var taluka = $('#taluka').val();
        var gramjuth = $('#gramjuth').val();
        var gram = $('#gram').val();
        if (role == 2 || role == 6) {
            if (prant) {
                $('.vibhagField').removeClass('d-none');
                getVibhagList(prant, vibhag);
            }
            if (vibhag) {
                $('.jillaField').removeClass('d-none');
                getJillaList(vibhag, jilla);
            }
        } else if (role == 3) {
            if (prant) {
                $('.vibhagField').removeClass('d-none');
                getVibhagList(prant, vibhag);
            }
            if (vibhag) {
                $('.jillaField').removeClass('d-none');
                getJillaList(vibhag, jilla);
            }
            if (jilla) {
                $('.talukaField').removeClass('d-none');
                getTalukaList(jilla, taluka);
            }
            if (taluka) {
                $('.gramjuthField').removeClass('d-none');
                getGramjuthList(taluka, gramjuth);
            }
            if (gramjuth) {
                $('.gramField').removeClass('d-none');
                getGramList(gramjuth, gram);
            }
        } else if (role == 4) {
            if (prant) {
                $('.vibhagField').removeClass('d-none');
                getVibhagList(prant, vibhag);
            }
            if (vibhag) {
                $('.jillaField').removeClass('d-none');
                getJillaList(vibhag, jilla);
            }
        }

        if (role == '1') {
            $('.emailField').removeClass('d-none');
            $('.passwordField').removeClass('d-none');
        } else if (role == '2' || role == '6') {
            $('.passwordField').removeClass('d-none');
            $('.prantField').removeClass('d-none');
            if ($('#prant').val() != '') {
                $('.vibhagField').removeClass('d-none');
            }
            if ($('#vibhag').val() != '') {
                $('.JillaField').removeClass('d-none');
            }
            if ($('#jilla').val() != '') {
                $('.gramJillaField').removeClass('d-none');
                $('.talukaField, .gramjuth_idField, .gramField').addClass('d-none');
            }
        } else if (role == '3') {
            $('.prantField').removeClass('d-none');
            if ($('#prant').val() != '') {
                $('.vibhagField').removeClass('d-none');
            }
            if ($('#vibhag').val() != '') {
                $('.JillaField').removeClass('d-none');
            }
            if ($('#jilla').val() != '') {
                $('.talukaField').removeClass('d-none');
            }
            if ($('#taluka').val() != '') {
                $('.gramjuth_idField').removeClass('d-none');
            }
            if ($('#gramjuth').val() != '') {
                $('.gramField').removeClass('d-none');
            }
        } else if (role == '4') {
            $('.emailField').removeClass('d-none');
            $('.passwordField').removeClass('d-none');
            $('.prantField').removeClass('d-none');
            if ($('#prant').val() != '') {
                $('.vibhagField').removeClass('d-none');
            }
        } else if (role == '5') {
            $('.emailField').removeClass('d-none');
            $('.passwordField').removeClass('d-none');
            $('.prantField').removeClass('d-none');
        }

    });

    $('#selectAll').click(function() {
        $('.selectAllGram').prop('checked', this.checked);
    });

    function getCheckboxList() {
        if ($('.selectAllGram:checked').length === $('.selectAllGram').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    }

    function getVibhagList(id, vibhag) {
        var role = $('#role').val();
        if ((role == '2' || role == '3' || role == '4' || role == '6') && $('#prant').val()) {
            $(".vibhagField").removeClass("d-none");
            $(".JillaField, .talukaField, .gramjuth_idField, .gramField, .gramJillaField").addClass("d-none");
            $("#jilla option").remove();
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();
        } else {
            $(".vibhagField, .JillaField, .talukaField, .gramjuth_idField, .gramField, .gramJillaField").addClass("d-none");
            $('#vibhag option').remove();
            $("#jilla option").remove();
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();
            return false;
        }

        $.ajax({
            url: "{{ url('vibhag-list') }}",
            type: 'GET',
            data: {
                prantId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Vibhag of " + $('#prant').find(':selected').text() + "</option>";
                if (response) {
                    var selected = '';
                    $.each(response, function(key, val) {
                        if (val.id == vibhag) {
                            selected = 'selected';
                        } else {
                            selected = '';
                        }
                        html += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });
                    $('#vibhag').html(html);
                }
            }
        });
    }

    function getJillaList(id, jilla) {
        var role = $('#role').val();
        if ((role == '2' || role == '3' || role == '6') && $('#vibhag').val()) {
            $(".JillaField").removeClass("d-none");
            $(".talukaField, .gramjuth_idField, .gramField, .gramJillaField").addClass("d-none");
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();
        } else {
            $(".JillaField, .talukaField, .gramjuth_idField, .gramField, .gramJillaField").addClass("d-none");
            $("#jilla option").remove();
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();

            return false;
        }

        $.ajax({
            url: "{{ url('jilla-list') }}",
            type: 'GET',
            data: {
                vibhagId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Jilla of " + $('#vibhag').find(':selected').text() + "</option>";
                if (response) {
                    var selected = '';
                    $.each(response, function(key, val) {
                        if (val.id == jilla) {
                            selected = 'selected';
                        } else {
                            selected = '';
                        }
                        html += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });
                    $('#jilla').html(html);
                } else {
                    $('#jilla').html('');
                }
                $('#taluka,#gramjuth,#gram').removeAttr('selected').find('option:first').attr('selected', 'selected');
            }
        });
    }

    function getTalukaList(id, taluka) {
        var role = $('#role').val();
        if (role == '3' && $('#jilla').val()) {
            $(".talukaField").removeClass("d-none");
            $(".gramjuth_idField, .gramField, .gramJillaField").addClass("d-none");
            $("#gramjuth option").remove();
            $("#gram option").remove();
        } else if (role == '2' || role == '6') {
            getJillaGramList(id);
        } else {
            $(".talukaField, .gramjuth_idField, .gramField, .gramJillaField").addClass("d-none");
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();

            return false;
        }

        $.ajax({
            url: "{{ url('taluka-list') }}",
            type: 'GET',
            data: {
                jillaId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Taluka of " + $('#jilla').find(':selected').text() + "</option>";
                if (response) {
                    var selected = '';
                    $.each(response, function(key, val) {
                        if (val.id == taluka) {
                            selected = 'selected';
                        } else {
                            selected = '';
                        }
                        html += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });
                    $('#taluka').html(html);
                } else {
                    $('#taluka').html('');
                }
            }
        });
    }

    function getGramjuthList(id, gramjuth) {
        if (id) {
            $(".gramjuth_idField").removeClass("d-none");
            $(".gramField").addClass("d-none");
            $("#gram option").remove();
        } else {
            $(".gramjuth_idField, .gramField").addClass("d-none");
            $("#gramjuth option").remove();
            $("#gram option").remove();
            return false;
        }

        $.ajax({
            url: "{{ url('gramjuth-list') }}",
            type: 'GET',
            data: {
                talukaId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Gramjuth of " + $('#taluka').find(':selected').text() + "</option>";
                if (response) {
                    var selected = '';
                    $.each(response, function(key, val) {
                        if (val.id == gramjuth) {
                            selected = 'selected';
                        } else {
                            selected = '';
                        }
                        html += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });
                    $('#gramjuth').html(html);
                } else {
                    $('#gramjuth').html(html);
                }
            }
        });
    }

    function getGramList(id, gram) {
        var role = $('#role').val();
        if (role != '3') {
            return false;
        }

        if (id) {
            $(".gramField").removeClass("d-none");
        } else {
            $(".gramField").addClass("d-none");
            $("#gram option").remove();
            return false;
        }

        $.ajax({
            url: "{{ url('gram-list') }}",
            type: 'GET',
            data: {
                gramjuthId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Gram of " + $('#gramjuth').find(':selected').text() + "</option>";
                if (response) {
                    var selected = '';
                    $.each(response, function(key, val) {
                        if (val.id == gram) {
                            selected = 'selected';
                        } else {
                            selected = '';
                        }
                        html += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });
                    $('#gram').html(html);
                } else {
                    $('#gram').html(html);
                }
            }
        });
    }

    function getJillaGramList(id) {
        var jilla = $('#jilla').val();

        if ($('#jilla').val()) {
            $('#selectAll').prop('checked', false);
            $(".gramJillaField").removeClass("d-none");
        } else {
            $('#selectAll').prop('checked', false);
            $(".gramJillaField").addClass("d-none");
        }
        $.ajax({
            url: "{{ url('jilla-gram-list') }}",
            type: 'GET',
            data: {
                jillaId: id
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                if (response != '') {
                    html += "<table class='table-responsive'><tbody><tr>";
                    $.each(response, function(key, value) {
                        html += "<td><div class='d-flex fw-bold h-100'>";
                        html += "<div class='form-check form-check-custom form-check-solid me-2'>";
                        html += "<input class='form-check-input selectAllGram' type='checkbox' name='gramId[]' value='" + value.id + "' id='" + value.id + "'" + " onclick='getCheckboxList()' />";
                        html += "<label class='form-check-label mt-2' for='" + value.id + "'>" + value.name + "</label>";
                        html += "</div></div></td>";

                        // Add a new row every 6 columns
                        if ((key + 1) % 6 === 0) {
                            html += "</tr><tr>";
                        }
                    });

                    html += "</tr></tbody></table>";
                    html += "<span class='help-block mt-2'><strong>Note :</strong>You can select more than one gram.</span>";
                    $('#gramList').html(html);
                } else {
                    $('.form-check-solid').addClass('d-none');
                    html += "<span class='help-block mt-2'>No record found.</span>";
                }
                $('#gramList').html(html);
            }
        });
    }

    function CheckEmail(email) {
        $.ajax({
            url: "{{ url('check-email') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                user_email: email
            },
            dataType: "JSON",
            success: function(response) {
                if (response.status == 1) {
                    $('.checkEmail').css('color', 'red');
                    $('.checkEmail').text(response.messages);
                    $('.user').addClass('disabled', true);
                } else {
                    $('.checkEmail').text('');
                    $('.user').removeClass('disabled', true);
                }
            }
        });
    }

    function CheckMobile(mobile) {
        $.ajax({
            url: "{{ url('check-mobile') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                user_mobile: mobile
            },
            dataType: "JSON",
            success: function(response) {
                if (response.status == 1) {
                    $('.checkMobile').css('color', 'red');
                    $('.checkMobile').text(response.messages);
                    $('input[type=submit]').addClass('disabled', true);
                } else {
                    $('.checkMobile').text('');
                    $('input[type=submit]').removeClass('disabled', true);
                }
            }
        });
    }

    function checkRole() {
        var role = $('#role').val();
        if (role == '2' || role == '6') {
            $('#email').val('');
            $('.passwordField, .JillaField, .prantField').removeClass('d-none');
            $("#prant").val(null).trigger('change');
            $('.emailField, .vibhagField, .JillaField, .talukaField, .gramjuth_idField, .gramField').addClass('d-none');
        } else if (role == '3') {
            $('#email').val('');
            $('.prantField').removeClass('d-none');
            $(".emailField, .passwordField, .vibhagField, .JillaField, .talukaField, .gramjuth_idField, .gramField, .emailField, .gramJillaField").addClass('d-none');
            $("#prant").val(null).trigger('change');
        } else if (role == '4') {
            $('.emailField, .passwordField, .prantField').removeClass('d-none');
            $("#prant").val(null).trigger('change');
            $('.vibhagField, .JillaField, .talukaField, .gramjuth_idField, .gramField, .gramJillaField').addClass('d-none');
        } else if (role == '5') {
            $('.emailField, .passwordField, .prantField').removeClass('d-none');
            $("#prant").val(null).trigger('change');
            $('.vibhagField, .JillaField, .talukaField, .gramjuth_idField, .gramField, .gramJillaField').addClass('d-none');
        } else if (role == '1') {
            $('.emailField').removeClass('d-none');
            $('.passwordField').removeClass('d-none');
            $('.prantField, .vibhagField, .JillaField, .talukaField, .gramjuth_idField, .gramField, .gramJillaField').addClass('d-none');
        } else {
            $('.passwordField').removeClass('d-none');
            $('.prantField, .vibhagField, .JillaField, .talukaField, .gramjuth_idField, .gramField, .gramJillaField').addClass('d-none');
        }
    }
</script>
@endsection