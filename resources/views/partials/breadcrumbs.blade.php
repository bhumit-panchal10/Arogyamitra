<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $title }}</h1>
<!--end::Title-->
<!--begin::Separator-->
<span class="h-20px border-gray-300 border-start mx-4"></span>

<ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
    <!--begin::Item-->
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Dashboard</a>
    </li>

    @if (collect(request()->segments())->last() == 'home')
    @else
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-200 w-5px h-2px"></span>
        </li>
        @php
            $route = '';
        @endphp
        @if ($title == 'Jilla')
            @php
                $route = route('jilla.index');
            @endphp
        @elseif($title == 'Vibhag')
            @php
                $route = route('vibhag.index');
            @endphp
        @elseif($title == 'Taluka')
            @php
                $route = route('taluka.index');
            @endphp
        @elseif($title == 'Gramjuth')
            @php
                $route = route('gramjuth.index');
            @endphp
        @elseif($title == 'Gram')
            @php
                $route = route('grams.index');
            @endphp
        @elseif($title == 'Users')
            @php
                $route = route('users.index');
            @endphp
        @elseif($title == 'Change Password')
            @php
                $route = route('password.create');
            @endphp
        @elseif($title == 'Medicine')
            @php
                $route = route('medicines.index');
            @endphp
        @elseif($title == 'Medicine Request')
            @php
                $route = route('medicineRequest.index');
            @endphp
        @elseif($title == 'Medicine Stock')
            @php
                $route = route('report.medicines.index');
            @endphp
        @elseif($title == 'Medicine Order')
            @php
                $route = route('order.medicines.index');
            @endphp
        @endif

        <li class="breadcrumb-item text-muted">
            @if ($title == 'Medicine Stock')
                <span class="breadcrumb-item text-dark">{{ $title }}</span>
            @elseif($title == 'Medicine Order')
                <span class="breadcrumb-item text-dark">{{ $title }}</span>
            @elseif($title == 'Change Password')
                <span class="breadcrumb-item text-dark">{{ $title }}</span>
            @elseif($title == 'Backend Report')
                <span class="breadcrumb-item text-dark">{{ $title }}</span>
            @elseif($title == 'Stockiest Report')
                <span class="breadcrumb-item text-dark">{{ $title }}</span>
            @elseif($title == 'App User Report')
                <span class="breadcrumb-item text-dark">{{ $title }}</span>
            @else
                <a href="{{ $route }}" class="text-muted text-hover-primary">{{ $title }}</a>
            @endif
        </li>
        @if ($title == 'Medicine Stock')
        @elseif($title == 'Medicine Order')

        @elseif($title == 'Change Password')

        @elseif($title == 'Backend Report')

        @elseif($title == 'Stockiest Report')

        @elseif($title == 'App User Report')
        @else
            <li class="breadcrumb-item">
                <span class="bullet bg-gray-200 w-5px h-2px"></span>
            </li>
            @if (Request::is(Request::segment(1)) ||
                    Request::is(Request::segment(2) . '/*') ||
                    Request::segment(2) == 'beneficiaries')
                @if (Request::segment(1) == 'privacy-policy-arogya-mitra')
                @else
                    <li class="breadcrumb-item text-dark">List of {{ $title }}</li>
                @endif
            @elseif(Request::is(Request::segment(1) . '/create'))
                <li class="breadcrumb-item text-dark">Add {{ $title }}</li>
            @elseif(Request::is(Request::segment(1) . '/*/edit'))
                <li class="breadcrumb-item text-dark">Edit {{ $title }}</li>
            @elseif(Request::is(Request::segment(1) . '/*'))
                <li class="breadcrumb-item text-dark">Show {{ $title }}</li>
                {{-- @if ($title != 'Beneficiaries')
        @php
            $id = Request::segment(2);
            $query = DB::table(strtolower($title))->where('id', '=', $id)->select('*')->first();
        @endphp
        @if ($title == 'User')
            <li class="breadcrumb-item text-dark">View ({{ !empty($query) && $query->name  ? $query->name : '' }})</li>
        @endif
    @endif --}}
            @endif
        @endif
    @endif
    <!--end::Item-->
</ul>
