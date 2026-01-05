@extends('layouts.app')

@section('head')
<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="card-body md-5">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                {!! Form::open(['route' => 'report.beneficiaries', 'method' => 'POST', 'class' => 'row row-cols-lg-auto g-3 align-items-center', 'autocomplete' => 'off']) !!}
                @csrf
                <input type="hidden" id="filterType" name="filterType" value="{{ $type }}">

                @if (Auth::user()->role == 1)
                <div class="col-6 col-sm-2" style="width: 20%;">
                    <label class="form-label fs-6 fw-bolder text-dark">Prant</label>
                    {{ Form::select('prant_id', $prant, $selPrant, ['class' => 'form-select', 'id' => 'prant', 'data-control' => 'select2', 'onchange' => 'getVibhagList(this.value)', 'placeholder' => 'Select Prant']) }}
                </div>
                @endif

                @if (Auth::user()->role == 5 || Auth::user()->role == 1)
                <div class="col-6 col-sm-2" style="width: 20%;">
                    <label class="form-label fs-6 fw-bolder text-dark">Vibhag</label>
                    {{ Form::select('vibhag_id', $vibhags, $selVibhag, ['class' => 'form-select', 'id' => 'vibhag', 'data-control' => 'select2', 'onchange' => 'getJillaList(this.value)', 'placeholder' => 'Select Vibhag']) }}
                </div>
                @endif
                <div class="col-6 col-sm-2" style="width: 20%;">
                    <label class="form-label fs-6 fw-bolder text-dark">Jilla</label>
                    {{ Form::select('jilla_id', $jilla, $selJilla, ['class' => 'form-select', 'id' => 'jilla', 'data-control' => 'select2', 'onchange' => 'getTalukaList(this.value)', 'placeholder' => 'Select Jilla']) }}
                </div>
                <div class="col-6 col-sm-2" style="width: 20%;">
                    <label class="form-label fs-6 fw-bolder text-dark">Taluka</label>
                    {{ Form::select('taluka_id', $taluka, $selTaluka, ['class' => 'form-select', 'id' => 'taluka', 'data-control' => 'select2', 'onchange' => 'getGramjuthList(this.value)', 'placeholder' => 'Select Taluka']) }}
                </div>

                <div class="col-6 col-sm-2" style="width: 20%;">
                    <label class="fs-6 form-label fw-bolder text-dark">Gramjuth</label>
                    {{ Form::select('gramjuth_id', $gramjuth, $selGramjuth, ['class' => 'form-select', 'id' => 'gramjuth', 'data-control' => 'select2', 'placeholder' => 'Select Gramjuth', 'onchange' => 'getGramList(this.value)']) }}
                </div>

                <div class="col-6 col-sm-2 fv-row mt-3" style="width: 25%;">
                    <label class="fs-6 fw-bolder text-dark mb-2">Date</label>
                    <input type="text" class="form-control" id="date_range" readonly placeholder="Select Dates" name="date_range" value="" />
                </div>

                <div class="col-2md-5 aqua mt-5">
                    {!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-primary me-2 mt-6']) !!}
                    <a href="" class="btn btn-light me-2 mt-6">Reset</a>
                    <button type="button" id="exportButton" class="btn btn-info me-2 mt-6" onclick="exportCSV('beneficiary')">Export</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                </svg>
            </span>
            <input type="text" id="searchInput" data-kt-user-table-filter="search" value="" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
        </div>
    </div>
    <div class="card-body table-responsive">
        <table id="beneficiary" class="table align-middle table-row-dashed fs-6 gy-5 pe-2">
            <thead>
                <tr class="fw-bolder fs-7 text-uppercase">
                    <th class="text-center">{{ trans('messages.dashboard.fields.serial_no') }}</th>
                    <th class="text-center">{{ $response['location_name'] }}</th>
                    <!-- <th class="text-center">Avg. Number of beneficiary</th> -->
                    <th class="text-center">Number of beneficiary</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @if($patientResp)
                    @php
                    $j = 1;
                    @endphp
                    @for ($i = 0; $i < count($patientResp); $i++)
                    <tr class="text-gray-600 fw-bold text-uppercase">
                        <td class="text-center">{{ $j }}</td>
                        <td class="text-center">{{ $patientResp[$i]['name'] }}</td>
                        <td class="text-center">{{ $patientResp[$i]['number_of_beneficiary'] }}</td>
                    </tr>
                        @php
                        $j++
                        @endphp
                        @endfor
                    <tfoot>
                        <tr class="text-gray-600 fw-bold text-uppercase">
                            <td class="text-center"></td>
                            <td class="text-center"><b>Total</b></td>
                            <td class="text-center">{{ $total }}</td>
                        </tr>
                    </tfoot>
                @else
                <tr class="text-gray-600 fw-bold text-uppercase">
                    <td colspan="3" class="text-center">No record found</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#date_range").flatpickr({
            mode: "range",
            defaultDate: ["{{ $startDate }}", "{{ $endDate }}"],
            dateFormat: "d-m-Y",
            maxDate: "today"
        });

        const table = $('#beneficiary').DataTable({
            paginate: true,
            pageLength: 25,
            searching: true,
            order: [],
        });

        // Apply search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#beneficiary').DataTable().search($(this).val()).draw();
        });

        var prantName = $('#prant').find(':selected').text();
        var vibhagName = $('#vibhag').find(':selected').text();
        var jillaName = $('#jilla').find(':selected').text();
        var vibhag = $('#vibhag');
        var jilla = $('#jilla');
        var vibhagVal = $('#vibhag').val();

        // Check if prantName is not 'Select Prant'
        if (prantName !== 'Select Prant') {

            // Add a new placeholder option
            vibhag.prepend("<option value=''>Select Vibhag " + prantName + "</option>");

            // Remove the second option
            vibhag.find('option:eq(1)').remove();
        }

        if (prantName !== 'Select Prant' && vibhagVal == '') {

            // Add a new placeholder option
            vibhag.prepend("<option value=''>Select Vibhag " + prantName + "</option>");

            // Remove the second option
            vibhag.find('option:eq(1)').remove();
        }

        if (vibhagName !== 'Select Prant' && vibhagVal !== '') {

            // Add a new placeholder option
            jilla.prepend("<option value=''>Select Jilla " + vibhagName + "</option>");

            // Remove the second option
            jilla.find('option:eq(1)').remove();

            $('#prant').on('change', function() {

                // Clear all existing options except the default option at position 0
                jilla.find('option:not(:first)').remove();

                // Add a default option
                jilla.prepend("<option value='' selected>Select Jilla</option>");

                //remove second option
                jilla.find('option:eq(1)').remove();
            });
        }
    });

    function getVibhagList(id) {
        if (id) {
            $("#filterType").val("vibhag");
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
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#vibhag').html(html);
                    } else {
                        $('#vibhag').html('');
                    }
                }
            });
            $('#jilla').html("<option value=''> Select Jilla </option>");
            $('#taluka').html("<option value=''> Select Taluka </option>");
            $('#gramjuth').html("<option value=''> Select Gramjuth </option>");
            $('#gram').html("<option value=''> Select Gram </option>");
        } else {
            $("#filterType").val("");

            $('#vibhag').html("<option value=''> Select Vibhag</option>");
            $('#jilla').html("<option value=''> Select Jilla </option>");
            $('#taluka').html("<option value=''> Select Taluka </option>");
            $('#gramjuth').html("<option value=''> Select Gramjuth </option>");
            $('#gram').html("<option value=''> Select Gram </option>");
        }
    }

    function getJillaList(id) {
        if (id) {
            $("#filterType").val("jilla");

            $.ajax({
                url: "{{ url('jilla-list') }}",
                type: 'GET',
                data: {
                    vibhagId: id
                },
                dataType: "JSON",
                success: function(response) {
                    var html = '';
                    if (response) {
                        html += "<option value=''> Select Jilla of " + $('#vibhag').find(':selected').text() + "</option>";
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#jilla').html(html);
                    } else {
                        $('#jilla').html('');
                    }
                }
            });
            $('#taluka').html("<option value=''> Select Taluka </option>");
            $('#gramjuth').html("<option value=''> Select Gramjuth </option>");
            $('#gram').html("<option value=''> Select Gram </option>");
        } else {
            $("#filterType").val("vibhag");

            $('#jilla').html("<option value=''> Select Jilla </option>");
            $('#taluka').html("<option value=''> Select Taluka </option>");
            $('#gramjuth').html("<option value=''> Select Gramjuth </option>");
            $('#gram').html("<option value=''> Select Gram </option>");
        }
    }

    function getTalukaList(id) {
        if (id) {
            $("#filterType").val("taluka");

            $.ajax({
                url: "{{ url('taluka-list') }}",
                type: 'GET',
                data: {
                    jillaId: id
                },
                dataType: "JSON",
                success: function(response) {
                    var html = '';
                    if (response) {
                        html += "<option value=''> Select Taluka of " + $('#jilla').find(':selected').text() + "</option>";
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#taluka').html(html);
                    } else {
                        $('#taluka').html('');
                    }
                }
            });
            $('#gramjuth').html("<option value=''> Select Gramjuth </option>");
            $('#gram').html("<option value=''> Select Gram </option>");
        } else {
            $("#filterType").val("jilla");

            $('#taluka').html("<option value=''> Select Taluka </option>");
            $('#gramjuth').html("<option value=''> Select Gramjuth </option>");
            $('#gram').html("<option value=''> Select Gram </option>");
        }
    }

    function getGramjuthList(id) {
        if (id) {
            $("#filterType").val("gramjuth");

            $.ajax({
                url: "{{ url('gramjuth-list') }}",
                type: 'GET',
                data: {
                    talukaId: id
                },
                dataType: "JSON",
                success: function(response) {
                    var html = '';
                    if (response) {
                        html += "<option value=''> Select Gramjuth of " + $('#taluka').find(':selected').text() + "</option>";
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#gramjuth').html(html);
                    } else {
                        $('#gramjuth').html(html);
                    }
                }
            });
            $('#gram').html("<option value=''> Select Gram </option>");
        } else {
            $("#filterType").val("taluka");

            $('#gramjuth').html("<option value=''> Select Gramjuth </option>");
            $('#gram').html("<option value=''> Select Gram </option>");
        }
    }

    function getGramList(id) {
        if (id) {
            $("#filterType").val("gram");
            $.ajax({
                url: "{{ url('gram-list') }}",
                type: 'GET',
                data: {
                    gramjuthId: id
                },
                dataType: "JSON",
                success: function(response) {
                    var html = '';
                    if (response) {
                        html += "<option value=''> Select Gram of " + $('#gramjuth').find(':selected').text() + "</option>";
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#gram').html(html);

                    } else {
                        $('#gram').html(html);
                    }
                }
            });
        } else {
            $("#filterType").val("gramjuth");
            $('#gram').html("<option value=''> Select Gram </option>");
        }
    }

    function exportCSV(beneficiariesTable) {
        var table = document.getElementById(beneficiariesTable);
        var rows = table.querySelectorAll('tbody tr');
        var csvContent = "data:text/csv;charset=utf-8,";

        var headers = Array.from(table.querySelectorAll('thead th')).map(header => header.textContent.trim());
        csvContent += headers.join(', ') + '\n';

        rows.forEach(row => {
            var rowData = Array.from(row.children).map(cell => cell.textContent.trim());
            csvContent += rowData.join(',') + '\n';
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "beneficiary.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    /* $(function() {
        $('input[name="daterange"]').daterangepicker({
            "autoApply": false,
            "showCustomRangeLabel": false,
            "minDate": "01/04/2017",
            "maxDate": "04/10/2017",
            "dateLimit": {
                "days": 60
            }

        }, function(start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
        });

        $(window).scroll(function() {
            if ($('input[name="daterange"]').length) {
                $('input[name="daterange"]').daterangepicker("close");
            }
        });
    }); */
</script>
@endsection