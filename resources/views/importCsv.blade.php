@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body p-lg-17">
        <div class="row mb-3">
            <div class="col-md-12 pe-lg-10">
                <div class="d-flex flex-column mb-3 fv-row">
                    <form action="{{ route('importCsv') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column mb-3 fv-row">
                            <input type="file" name="csv_file" class="form-control mb-2">
                            <p class="text-muted">Only CSV files are allowed</p>
                        </div>
                        <button class="btn btn-xs btn-success">{{ trans('messages.users.fields.import') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
    .form-control {
        width: 40%;
        height: 40px;
        padding: 10px;
    }
</style>
@endsection
