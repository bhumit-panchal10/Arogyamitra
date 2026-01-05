@extends('layouts.app')

@section('head')
<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
<div class="row">
    <div class="card-body md-5">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                {!! Form::open(['route' => 'users.index', 'method' => 'POST', 'class' => 'row row-cols-lg-auto g-3 align-items-center', 'autocomplete' => 'off']) !!}
                @csrf
                <div class="col-12">
                    <select id="status" name="status" class="form-control w-150px" data-kt-select2="true">
                        <option value="">Select Status</option>
                        <option value="Active" {{ request()->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Deactive" {{ request()->status == 'Deactive' ? 'selected' : '' }}>Deactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <select id="role" name="role" class="form-control w-150px" data-kt-select2="true" onchange="checkRole(this.value)">
                        <option value="">Select Role</option>
                        @if(Auth::user()->role == 1)
                            <option value="1" {{ request()->role == '1' ? 'selected' : '' }}>Backend User</option>
                            <option value="5" {{ request()->role == '5' ? 'selected' : '' }}>Prant User</option>
                            <option value="4" {{ request()->role == '4' ? 'selected' : '' }}>Vibhag User</option>
                            <option value="2" {{ request()->role == '2' ? 'selected' : '' }}>App User</option>
                            <option value="3" {{ request()->role == '3' ? 'selected' : '' }}>Arogya Mitra User</option>
                            <option value="6" {{ request()->role == '6' ? 'selected' : '' }}>Stockiest User</option>
                        @else
                            <option value="2" {{ request()->role == '2' ? 'selected' : '' }}>App User</option>
                            <option value="3" {{ request()->role == '3' ? 'selected' : '' }}>Arogya Mitra User</option>
                        @endif
                    </select>
                </div>
                @if(Auth::user()->role == 1)
                    <div class="col-12 prantField d-none">
                        <select id="prant" name="prant" class="form-control w-150px" data-kt-select2="true" onchange="getVibhagList(this.value)">
                            <option value="">Select Prant</option>
                            @foreach ($prantList as $pKey => $pVal)
                                <option value="{{ $pVal->id }}" {{ $selPrant == $pVal->id ?  'selected' : ''  }}>{{ $pVal->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if(Auth::user()->role == 1 || Auth::user()->role == 5)
                <div class="col-12 vibhagField d-none">
                    <select id="vibhag" name="vibhag" class="form-control w-150px" data-kt-select2="true" onchange="getJillaList(this.value)">
                        <option value="">Select Vibhag</option>
                    </select>
                </div>
                @endif
                <div class="col-12 JillaField d-none">
                    <select id="jilla" name="jilla" class="form-control w-150px" data-kt-select2="true" onchange="getTalukaList(this.value)">
                        <option value="">Select Jilla</option>
                    </select>
                </div>
                <div class="col-12 talukaField d-none">
                    <select id="taluka" name="taluka" class="form-control w-150px" data-kt-select2="true" onchange="getGramjuthList(this.value)">
                        <option value="">Select Taluka</option>
                    </select>
                </div>
                <div class="col-12 gramjuthField d-none">
                    <select id="gramjuth" name="gramjuth" class="form-control w-150px" data-kt-select2="true" onchange="getGramList(this.value)">
                        <option value="">Select Gramjuth</option>
                    </select>
                </div>
                <div class="col-12 gramField d-none">
                    <select id="gram" name="gram" class="form-control w-150px" data-kt-select2="true">
                        <option value="">Select Gram</option>
                    </select>
                </div>
            </div>
            <div class="card-title">
                <div class="col-12">
                    {!! Form::submit('Submit', ['class' => 'btn btn-primary me-2']) !!}
                    <a href="{{route('users.index')}}" class="btn btn-light">Reset</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                    </svg>
                </span>
                <input type="text" id="searchInput" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
            </div>
        </div>
    </div>

    <div class="card-body pt-0 table-responsive">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="userTable">
            <thead>
                <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                    <th class="text-center">{{ trans('messages.users.fields.serial_no') }}</th>
                    <th class="text-center">{{ trans('messages.users.fields.name') }}</th>
                    @if(Auth::user()->role == '1')
                    <th class="text-center">{{ trans('messages.users.fields.email') }}</th>
                    @endif
                    <th class="text-center">{{ trans('messages.users.fields.mobile_no') }}</th>
                    <th class="text-center">{{ trans('messages.users.fields.role') }}</th>
                    @if(Auth::user()->role == '1' || Auth::user()->role == '4' || Auth::user()->role == '5')
                    <th class="text-center">{{ trans('messages.users.fields.action') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @forelse($users as $user)
                <tr>
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td class="text-center">{{ $user->name }}</td>
                    @if(Auth::user()->role == '1')
                    <td class="text-center">{{ $user->email ? $user->email : '-'  }}</td>
                    @endif
                    <td class="text-center">{{ $user->mobile_no }}</td>
                    <td class="text-center">{{ $user->role_name }}</td>
                    @if(Auth::user()->role == '1')
                    <td class="text-center">
                        <div class="badge badge-light-primary fw-bolder">
                            @if(Auth::user()->role == '1')
                            <span class="btn btn-info" style="width: 60px;" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="{{ $user->location_name }}"><i class="fa fa-info"></i></span>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="{{ $user->status == 'Active' ? 'Active' : 'Deactive' }}" onclick="changeStatus('{{$user->status}}', '{{$user->id}}', '{{$user->gram_id}}')" class="btn btn-xs btn-success">
                                @if($user->status == 'Active')
                                <i class="fas fa-regular fa-lock-open fa-5x" aria-hidden="true"></i>
                                @else
                                <i class="fas fa-regular fa-lock fa-5x" aria-hidden="true"></i>
                                @endif
                            </a>
                            @endif
                            <a class="btn btn-primary" data-toggle="tooltip" title="Show" data-bs-custom-class="tooltip-dark" href="{{ route('users.show', $user->id) }}"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-info" data-toggle="tooltip" title="Edit" data-bs-custom-class="tooltip-dark" href="{{ route('users.edit', $user->id) }}"><i class="fa fa-edit"></i></a>

                            {!! Form::open([
                            'route' => ['users.destroy', $user->id],
                            'method' => 'POST',
                            'style' => 'display:inline-block;',
                            'onsubmit' => "return confirm('Are you sure you want to delete this user?');"
                            ]) !!}

                            {!! Form::button('<i class="fa fa-trash"></i>', ['data-toggle'=>"tooltip", 'data-bs-custom-class'=>"tooltip-dark", 'title'=>"Delete", 'type' => 'submit', 'class' => 'btn btn-xs btn-danger user-delete']) !!}
                            {!! Form::close() !!}
                        </div>
                    </td>
                    @endif
                    @if(Auth::user()->role == '4' || Auth::user()->role == '5')
                    <td class="text-center">
                        <div class="badge badge-light-primary fw-bolder">
                            <span class="btn btn-info" style="width: 60px;" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="{{ $user->location_name }}"><i class="fa fa-info"></i></span>
                        </div>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="text-align: center;">No record found</td>
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
        var role = $('#role').val();
        var loginUserRole = '{{ Auth::user()->role }}';
        if (loginUserRole == 1) {
            if (role == '4') {
                var prant = '{{ $selPrant }}';
                $('.prantField').removeClass('d-none');
                if (prant) {
                    var vibhag = '{{ $selVibhag }}';
                    $('.vibhagField').removeClass('d-none');
                    vibhagLists(prant, vibhag);
                }
            } else if (role == '2' || role == '6') {
                var prant = '{{ $selPrant }}';
                $('.prantField').removeClass('d-none');
                if (prant) {
                    $('.vibhagField').removeClass('d-none');
                    var vibhag = '{{ $selVibhag }}';
                    vibhagLists(prant, vibhag);

                    if (vibhag) {
                        $('.JillaField').removeClass('d-none');
                        var jilla = '{{ $selJilla }}';
                        jillaLists(vibhag, jilla);
                    }
                }
            } else if (role == '3') {
                var prant = '{{ $selPrant }}';
                $('.prantField').removeClass('d-none');
                if (prant) {
                    var vibhag = '{{ $selVibhag }}';
                    var jilla = '{{ $selJilla }}';
                    var taluka = '{{ $selTaluka }}';
                    var gramjuth = '{{ $selGramjuth }}';
                    var gram = '{{ $selGram }}';

                    $('.vibhagField').removeClass('d-none');
                    vibhagLists(prant, vibhag);

                    if (vibhag) {
                        $('.JillaField').removeClass('d-none');
                        jillaLists(vibhag, jilla);
                    }
                    if (jilla) {
                        $('.talukaField').removeClass('d-none');
                        talukaLists(jilla, taluka);
                    }

                    if (taluka) {
                        $('.gramjuthField').removeClass('d-none');
                        gramjuthLists(taluka, gramjuth);
                    }
                    if (gramjuth) {
                        $('.gramField').removeClass('d-none');
                        gramLists(gramjuth, gram);
                    }
                }
            } else if (role == '5') {
                $('.prantField').removeClass('d-none');
            }
        } else if (loginUserRole == 4) {
            if (role == '2'  || role == '6') {
                var vibhag = '{{ Auth::user()->vibhag_id }}';
                if (vibhag) {
                    $('.JillaField').removeClass('d-none');
                    var jilla = '{{ $selJilla }}';
                    jillaLists(vibhag, jilla);
                }
            } else if (role == '3') {
                var vibhag = '{{ Auth::user()->vibhag_id }}';
                if (vibhag) {
                    var jilla = '{{ $selJilla }}';
                    var taluka = '{{ $selTaluka }}';
                    var gramjuth = '{{ $selGramjuth }}';
                    var gram = '{{ $selGram }}';

                    if (vibhag) {
                        $('.JillaField').removeClass('d-none');
                        jillaLists(vibhag, jilla);
                    }
                    if (jilla) {
                        $('.talukaField').removeClass('d-none');
                        talukaLists(jilla, taluka);
                    }

                    if (taluka) {
                        $('.gramjuthField').removeClass('d-none');
                        gramjuthLists(taluka, gramjuth);
                    }
                    if (gramjuth) {
                        $('.gramField').removeClass('d-none');
                        gramLists(gramjuth, gram);
                    }
                }
            }
        } else if (loginUserRole == 5) {
            if (role == '2' || role == '6') {
                var prant = '{{ Auth::user()->prant_id }}';
                if (prant) {
                    $('.vibhagField').removeClass('d-none');
                    var vibhag = '{{ $selVibhag }}';
                    vibhagLists(prant, vibhag);

                    if (vibhag) {
                        $('.JillaField').removeClass('d-none');
                        var jilla = '{{ $selJilla }}';
                        jillaLists(vibhag, jilla);
                    }
                }
            } else if (role == '3') {
                var prant = '{{ Auth::user()->prant_id }}';
                if (prant) {
                    var vibhag = '{{ $selVibhag }}';
                    var jilla = '{{ $selJilla }}';
                    var taluka = '{{ $selTaluka }}';
                    var gramjuth = '{{ $selGramjuth }}';
                    var gram = '{{ $selGram }}';

                    $('.vibhagField').removeClass('d-none');
                    vibhagLists(prant, vibhag);

                    if (vibhag) {
                        $('.JillaField').removeClass('d-none');
                        jillaLists(vibhag, jilla);
                    }
                    if (jilla) {
                        $('.talukaField').removeClass('d-none');
                        talukaLists(jilla, taluka);
                    }

                    if (taluka) {
                        $('.gramjuthField').removeClass('d-none');
                        gramjuthLists(taluka, gramjuth);
                    }
                    if (gramjuth) {
                        $('.gramField').removeClass('d-none');
                        gramLists(gramjuth, gram);
                    }
                }
            }
        }


        // Initialize DataTables with search input functionality
        $('#userTable').DataTable({
            paginate: true,
            searching: true,
            pageLength: 25,
            order: [],
            columnDefs: [{
                targets: 'no-search',
                searchable: false,
            }],
        });

        // Apply search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#userTable').DataTable().search($(this).val()).draw();
        });
    });

    function getVibhagList(parentId) {
        //prant selection
        var role = $('#role').val();
        if (role == '5') {
            return false;
        }
        if (parentId) {
            $(".vibhagField").removeClass("d-none");
            $(".JillaField, .talukaField, .gramjuthField, .gramField").addClass("d-none");
            $("#jilla option").remove();
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();
        } else {
            $(".vibhagField, .JillaField, .talukaField, .gramjuthField, .gramField").addClass("d-none");
            $('#vibhag option').remove();
            $("#jilla option").remove();
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();
            return false;
        }

        vibhagLists(parentId, '');
    }

    function getJillaList(vibhagId) {
        //vibhg selection
        var role = $('#role').val();
        if (role == '4'|| role == '5') {
            return false;
        }

        if (vibhagId) {
            $(".JillaField").removeClass("d-none");
            $(".talukaField, .gramjuthField, .gramField").addClass("d-none");
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();
        } else {
            $(".JillaField, .talukaField, .gramjuthField, .gramField").addClass("d-none");
            $("#jilla option").remove();
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();

            return false;
        }
        jillaLists(vibhagId, '');
    }

    function getTalukaList(jillaId) {
        //jilla selection
        var role = $('#role').val();
        if (role == '2' || role == '4'|| role == '5' || role == '6') {
            return false;
        }
        if (jillaId) {
            $(".talukaField").removeClass("d-none");
            $(".gramjuthField, .gramField").addClass("d-none");
            $("#gramjuth option").remove();
            $("#gram option").remove();
        } else {
            $(".talukaField, .gramjuthField, .gramField").addClass("d-none");
            $("#taluka option").remove();
            $("#gramjuth option").remove();
            $("#gram option").remove();
            return false;
        }
        talukaLists(jillaId, '');
    }

    function getGramjuthList(talukaId) {
        //taluka selection
        var role = $('#role').val();
        if (role == '2' || role == '4'|| role == '5' || role == '6') {
            return false;
        }

        if (talukaId) {
            $(".gramjuthField").removeClass("d-none");
            $(".gramField").addClass("d-none");
            $("#gram option").remove();
        } else {
            $(".gramjuthField, .gramField").addClass("d-none");
            $("#gramjuth option").remove();
            $("#gram option").remove();
            return false;
        }
        gramjuthLists(talukaId, '');
    }

    function getGramList(gramjuthId) {
        //gramjuth selection
        var role = $('#role').val();
        if (role == '2' || role == '4'|| role == '5' || role == '6') {
            return false;
        }

        if (gramjuthId) {
            $(".gramField").removeClass("d-none");
        } else {
            $(".gramField").addClass("d-none");
            $("#gram option").remove();
            return false;
        }
        gramLists(gramjuthId, '');
    }


    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    function changeStatus(status, id, gram_id) {
        // alert(gram_id);
        if (confirm('Are you sure you want to change your status')) {
            $.ajax({
                url: "{{ url('change-user-status') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    user_id: id,
                    user_status: status,
                    gram_id: gram_id,
                },
                dataType: "JSON",
                success: function(response) {
                    if (response.status == 1) {
                        location.reload();
                    } else {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
                }
            });
        }
    }

    function checkRole() {
        var role = $("#role").val();
        if (role == "2"  || role == "3" || role == "4" || role == "5" || role == "1" || role == '6') {
            var loginUserRole = '{{ Auth::user()->role }}';
            if (loginUserRole == 5) {
                var prantId = '{{ Auth::user()->prant_id }}';
                vibhagLists(prantId, '');
                $(".vibhagField").removeClass("d-none");
                $(".JillaField, .talukaField, .gramjuthField, .gramField").addClass("d-none");
                $("#vibhag, #jilla, #taluka, #gramjuth, #gram").val(null).trigger("change");
            } else if (loginUserRole == 4) {
                var vibhagId = '{{ Auth::user()->vibhag_id }}';
                jillaLists(vibhagId, '');
                $(".JillaField").removeClass("d-none");
                $(".talukaField, .gramjuthField, .gramField").addClass("d-none");
                $("#prant, #vibhag, #jilla, #taluka, #gramjuth, #gram").val(null).trigger("change");
            } else if (loginUserRole == 1) {
                if (role == "1") {
                    $(".prantField, .vibhagField, .JillaField, .talukaField, .gramjuthField, .gramField").addClass("d-none");
                    $("#prant, #vibhag, #jilla, #taluka, #gramjuth, #gram").val(null).trigger("change");
                } else {
                    $(".prantField").removeClass("d-none");
                    $(".vibhagField, .JillaField, .talukaField, .gramjuthField, .gramField").addClass("d-none");
                    $("#prant, #vibhag, #jilla, #taluka, #gramjuth, #gram").val(null).trigger("change");
                }
            }
        } else {
            $(".prantField, .vibhagField, .JillaField, .talukaField, .gramjuthField, .gramField").addClass("d-none");
            $("#prant, #vibhag, #jilla, #taluka, #gramjuth, #gram").val(null).trigger("change");
        }
    }

    function vibhagLists(prantId, vibhag) {
        $.ajax({
            url: "{{ url('vibhag-list') }}",
            type: 'GET',
            data: {
                prantId: prantId
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

    function jillaLists(vibhagId, jilla) {
        $.ajax({
            url: "{{ url('jilla-list') }}",
            type: 'GET',
            data: {
                vibhagId: vibhagId
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
                }
            }
        });
    }

    function talukaLists(jillaId, taluka) {
        $.ajax({
            url: "{{ url('taluka-list') }}",
            type: 'GET',
            data: {
                jillaId: jillaId
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
                }
            }
        });
    }

    function gramjuthLists(talukaId, gramjuth) {
        $.ajax({
            url: "{{ url('gramjuth-list') }}",
            type: 'GET',
            data: {
                talukaId: talukaId
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
                }
            }
        });
    }

    function gramLists(gramjuthId, gram) {
        $.ajax({
            url: "{{ url('gram-list') }}",
            type: 'GET',
            data: {
                gramjuthId: gramjuthId
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
                }
            }
        });
    }
    // function exportCSV(userTable) {
    //     var table = document.getElementById(userTable);
    //     var rows = table.querySelectorAll('tbody tr');
    //     var csvContent = "data:text/csv;charset=utf-8,";

    //     var headers = Array.from(table.querySelectorAll('thead th')).map(header => header.textContent.trim());
    //     csvContent += headers.join(',') + '\n';

    //     rows.forEach(row => {
    //         var rowData = Array.from(row.children).map(cell => cell.textContent.trim());
    //         csvContent += rowData.join(',') + '\n';
    //     });

    //     var encodedUri = encodeURI(csvContent);
    //     var link = document.createElement("a");
    //     link.setAttribute("href", encodedUri);
    //     link.setAttribute("download", "User-List.csv");
    //     document.body.appendChild(link);
    //     link.click();
    //     document.body.removeChild(link);
    // }
    $(document).on('click','.redirectToUrl',function(){
    let getRedirectUrl = $(this).attr('data-redirect-url');
    window.location.href= getRedirectUrl;
  });
</script>
@endsection