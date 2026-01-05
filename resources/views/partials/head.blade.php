<meta charset="utf-8">

<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'ArogyaMitra Portal - Sewa Bharti') }}</title>

<meta name="description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta charset="utf-8" />
<link rel="shortcut icon" href="{{ url('assets/media/logos/logo.png') }}" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<link href="{{ url('assets/plugins/global/plugins.bundle.css')}} " rel="stylesheet" type="text/css" />
<link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
@yield('head')