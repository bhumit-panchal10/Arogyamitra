<!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <style>
        @font-face {
            font-family: "lohit";
            font-style: normal;
            font-weight: 400;
            src: url("{{ storage_path('/fonts/Lohit_Gujarati.ttf') }}");
        }
    </style>
</head>

<body>
    <span style="font-family: lohit;font-style: normal;text-align: center;">{{ trans('messages.list') }}</span>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" class="text-center">Jilla Name</th>
                <th scope="col" class="text-center">Created By</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-family: lohit;font-style: normal;text-align: center;">{{$jilla->jilla}}</td>
                <td style="font-family: lohit;font-style: normal;text-align: center;">{{$jilla->user}}</td>
            </tr>
        </tbody>
    </table>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">Taluka</th>
                <th scope="col">Gramjuth</th>
                <th scope="col">Gram</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile No.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getExportData as $val)
            <tr>
                <td style="font-family: lohit;font-style: normal;">{{ $val['taluka'] }}</td>
                <td style="font-family: lohit;font-style: normal;">{{ $val['gramjuth'] }}</td>
                <td style="font-family: lohit;font-style: normal;">{{ $val['gram'] }}</td>
                <td style="font-family: lohit;font-style: normal;">{{ $val['users_name'] }}</td>
                <td>{{ $val['users_email'] }}</td>
                <td>{{ $val['users_mobile'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>