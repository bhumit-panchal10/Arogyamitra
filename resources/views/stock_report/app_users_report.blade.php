@extends('layouts.app')

@section('head')
<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<style>
    .col-2 {
        width: 25%;
    }

    .col-md-6 {
        width: 16%;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            {!! Form::open(['route' => 'report.appUsers', 'method' => 'POST', 'class' => 'row row-cols-lg-auto g-3 align-items-center', 'autocomplete' => 'off']) !!}
            @csrf
            <div class="d-flex align-items-center ms-2">
            <input type="hidden" id="a" value="{{ request()->input('app_user_id') }}">
            {{ Form::select('app_user_id', $user, $selUser, ['class' => 'form-select', 'id' => 'user', 'data-control' => 'select2', 'placeholder' => 'Select App User', 'onchange' => 'getGramList(this.value)']) }}

            <input type="hidden" id="g" value="{{ request()->input('gram_id') }}">
            {{ Form::select('id', $gramName, $selectedGram, ['class' => 'form-select ms-2', 'id' => 'gram', 'data-control' => 'select2', 'placeholder' => 'Select Gram']) }}

                {!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-primary ms-4']) !!}
                    <a href="{{ route('report.appUsers') }}" class="btn btn-light me-2 ms-2">Reset</a>
                {!! Form::close() !!}
            </div>

        </div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <div class="medicine_list d-none"> Medicine Stock List</div>
            <table id="kt_customers_table" class="table align-middle table-row-dashed fs-6 gy-5 w-10px pe-2">
                <thead>
                    <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-center">{{ trans('messages.dashboard.fields.serial_no') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.medicine') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.current') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.quantity_type') }}</th>
                        <!-- <th class="text-center">{{ trans('messages.stock.fields.date') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.type') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.qty') }}</th> -->
                        <th class="text-center">{{ trans('messages.medicine.fields.action') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @forelse($medicines as $medicine)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $medicine->name }}</td>
                            <td class="text-center">{{ $medicine->qty }}</td>
                            <td class="text-center">{{ $medicine->qty_type }}</td>
                            <!-- <td class="text-center">{{ $medicine->date }}</td>
                            <td class="text-center">{{ $medicine->mode }}</td>
                            <td class="text-center">{{ $medicine->track_qty }}</td> -->
                            <td class="text-center">
                               <a href="{{ route('report.appUser-show', [$medicine->medicines_id, $selectedGram]) }}" class="btn btn-info"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">
                                No record found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>

    $(document).ready(function() {
        // Initialize DataTables with search input functionality
        $('#kt_customers_table').DataTable({
            paginate: true,
            pageLength: 25,
            searching: true,
            order: [],
            columnDefs: [{
                'targets': [0],
                'orderable': false,
                //targets: 'no-search',
                searchable: false,
            }],
        });

        // Apply search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#kt_customers_table').DataTable().search($(this).val()).draw();
        });

    });

    function getGramList(id, gram) {
        if (!id) {
            $('#gram').empty().append('<option value="">Select Gram</option>');
            // $("#gram").empty();
            return;
        }
        console.log("User ID:", id);
        $.ajax({
            url: "{{ url('user-gram-list') }}",
            type: 'GET',
            data: {
                userId : id,
            },
            dataType: "JSON",
            success: function(response) {
                var html = '';
                html += "<option value=''> Select Gram of App User " + $('#user').find(':selected').text() + "</option>";
                for (var gramId in response) {
                    if (response.hasOwnProperty(gramId)) {
                        var selected = (gramId == gram) ? 'selected' : '';
                        html += "<option value='" + gramId + "'" + selected + ">" + response[gramId] + "</option>";
                    }
                }
                $('#gram').html(html);
            }
        });
    }

</script>
@endsection