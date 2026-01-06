<ul class="nav nav-tabs nav-fill mb-4">

    <li class="nav-item">
        <a class="nav-link {{ request()->status == '1' || !request()->has('status') ? 'active' : '' }}"
            href="{{ route('medicineRequest.index', ['status' => 1]) }}">
            Pending Request
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->status == '2' ? 'active' : '' }}"
            href="{{ route('medicineRequest.index', ['status' => 2]) }}">
            Accepted Request
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->status == '0' ? 'active' : '' }}"
            href="{{ route('medicineRequest.index', ['status' => 0]) }}">
            Rejected Request
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->status == '3' ? 'active' : '' }}"
            href="{{ route('medicineRequest.index', ['status' => 3]) }}">
            Delivered Request
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('medicineRequest.index') ? 'active' : '' }}"
            href="{{ route('medicineRequest.index') }}">
            Reports
        </a>
    </li>

</ul>
