@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                {!! Form::model($gramjuth, ['method' => 'PUT', 'route' => ['gramjuth.update', $gramjuth->id]]) !!}
                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name', 'maxlength' => 20, 'oninput' => "this.value = this.value.replace(/[^a-zA-Z \\u0A80-\\u0AFF]/g, '')"]) !!}
                    @error('name')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('taluka', 'Taluka <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    {!! Form::select('taluka_id', $taluka, $gramjuth->taluka_id, ['data-control'=>'select2', 'placeholder' => 'Select Taluka', 'class' => 'form-control' . ($errors->has('taluka_id') ? ' is-invalid' : '')]) !!}
                    @error('taluka_id')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="d-flex flex-column mb-4 fv-row">
                    {!! htmlspecialchars_decode(Form::label('status', 'Status <span class="required"></span>', ['class' => 'control-label fs-5 fw-bold mb-2'])) !!}
                    <div class="radio-inline">
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '1', $gramjuth->status, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! Form::label('active', 'Active', ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('status', '0', $gramjuth->status, ['class' => 'form-check-input', 'id' => 'status1']) !!}
                            {!! Form::label('inactive', 'Inactive', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                    @error('status')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>

                {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
                <a href="{{ route('gramjuth.index') }}" class="btn btn-xs btn-danger">{{ trans('messages.gramjuth.fields.cancel') }}</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop
@section('javascript')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Event listener for the vibhag dropdown change
        $('#vibhag_id').on('change', function() {
            var vibhagId = $(this).val();
            getJillaList(vibhagId);
        });

        // Event listener for the jilla dropdown change
        $('#jilla_id').on('change', function() {
            var jillaId = $(this).val();
            getTalukaList(jillaId);
        });

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
                    html += "<option value=''>Select Jilla</option>";
                    if (response) {
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#jilla_id').html(html);
                    } else {
                        $('#jilla_id').html('');
                    }
                    $('#taluka_id').html('<option value="">Select taluka</option>');
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
                    html += "<option value=''>Select Taluka</option>";
                    if (response) {
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#taluka_id').html(html);
                    } else {
                        $('#taluka_id').html('');
                    }
                }
            });
        }
    });
</script>
@endsection