<!--begin::Aside-->
<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{ url('/') }}">
            <img alt="Logo" src="{{ url('assets/media/logos/sevabharti_login_text.png') }}" class="h-45px logo" />
        </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none">
                    <path opacity="0.5"
                        d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z"
                        fill="black" />
                    <path
                        d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z"
                        fill="black" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside toggler-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid">
        <!--begin::Aside Menu-->
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
            data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                id="#kt_aside_menu" data-kt-menu="true">
                <div class="menu-item">
                    <ul class="nav nav-pills nav-stacked" data-spy="affix">
                        <a class="menu-link {{ request()->is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <span class="menu-icon">
                                <i class="fas fa-home"></i>
                            </span>
                            <span class="menu-title">Dashboard</span><br>
                        </a>

                        <a class="menu-link {{ request()->is('users*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <span class="menu-title">Users</span><br>
                        </a>

                        <a class="menu-link {{ request()->is('medicines*') ? 'active' : '' }}"
                            href="{{ route('medicines.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-medkit"></i>
                            </span>
                            <span class="menu-title">Medicine</span>
                        </a>

                        @if (Auth::user()->role == '1')
                            <a class="menu-link {{ request()->is('medicine-stock*') ? 'active' : '' }}"
                                href="{{ route('medicineStock.index') }}">
                                <span class="menu-icon">
                                    <i class="fas fa-briefcase"></i>
                                </span>
                                <span class="menu-title">Medicine Stock</span>
                            </a>
                        @endif

                        <a class="menu-link {{ request()->is('medicineRequest*') ? 'active' : '' }}"
                            href="{{ route('medicineRequest.index', ['status' => 1]) }}">
                            <span class="menu-icon">
                                <i class="fas fa-prescription"></i>
                            </span>
                            <span class="menu-title">Medicine Request</span>
                        </a>
                        @if (Auth::user()->role == '1')
                            <a class="menu-link {{ request()->is('prant*') ? 'active' : '' }}"
                                href="{{ route('prant.index') }}">
                                <span class="menu-icon">
                                    <i class="fas fa-hotel"></i>
                                </span>
                                <span class="menu-title">Prant</span><br>
                            </a>

                            <a class="menu-link {{ request()->is('vibhag*') ? 'active' : '' }}"
                                href="{{ route('vibhag.index') }}">
                                <span class="menu-icon">
                                    <i class="fas fa-building"></i>
                                </span>
                                <span class="menu-title">Vibhag</span><br>
                            </a>

                            <a class="menu-link {{ request()->is('jilla*') ? 'active' : '' }}"
                                href="{{ route('jilla.index') }}">
                                <span class="menu-icon">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                </span>
                                <span class="menu-title">Jilla</span><br>
                            </a>

                            <a class="menu-link {{ request()->is('taluka*') ? 'active' : '' }}"
                                href="{{ route('taluka.index') }}">
                                <span class="menu-icon">
                                    <i class="fas fa-map-marked"></i>
                                </span>
                                <span class="menu-title">Taluka</span>
                            </a>

                            <a class="menu-link {{ request()->is('gramjuth*') ? 'active' : '' }}"
                                href="{{ route('gramjuth.index') }}">
                                <span class="menu-icon">
                                    <i class="fas fa-warehouse"></i>
                                </span>
                                <span class="menu-title">Gramjuth</span>
                            </a>
                            <a class="menu-link {{ request()->is('grams*') ? 'active' : '' }}"
                                href="{{ route('grams.index') }}">
                                <span class="menu-icon">
                                    <i class="fas fa-users"></i>
                                </span>
                                <span class="menu-title">Gram</span>
                            </a>
                        @endif
                    </ul>
                </div>
                @if (Auth::user()->role == '4' || Auth::user()->role == '5' || Auth::user()->role == '1')
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->segment(1) == 'report' ? 'show' : '' }}">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span class="menu-title">Reports</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is('report/medicines-stock*') ? 'active' : '' }}"
                                    href="{{ route('report.medicines.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Medicine Stock</span>
                                </a>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is('report/order/medicines-request*') ? 'active' : '' }}"
                                    href="{{ route('order.medicines.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Medicine Order</span>
                                </a>
                            </div>
                        </div>
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is('report/beneficiaries') ? 'active' : '' }}"
                                    href="{{ route('report.beneficiaries') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Beneficiaries</span>
                                </a>
                            </div>
                        </div>
                        @if (Auth::user()->role == '1')
                            <div class="menu-sub menu-sub-accordion">
                                <div data-kt-menu-trigger="click"
                                    class="menu-item menu-accordion {{ request()->segment(1) == 'report' ? 'show' : '' }}">
                                    <span class="menu-link">
                                        <span class="menu-title">Stock Update</span>
                                        <span class="menu-arrow"></span>
                                    </span>

                                    <div class="menu-sub menu-sub-accordion">
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->is('report/stock-report') || request()->is('report/stock-report-show*') ? 'active' : '' }}"
                                                href="{{ route('report.backend') }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">Backend Report</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="menu-sub menu-sub-accordion">
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->is('report/stockiest-stock-report*') || request()->is('report/stockiest-report-show*') ? 'active' : '' }}"
                                                href="{{ route('report.stockiest') }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">Stockiest Report</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="menu-sub menu-sub-accordion">
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->is('report/stock-report-appuser*') || request()->is('report/appUsers-report-show*') ? 'active' : '' }}"
                                                href="{{ route('report.appUsers') }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">App User Report</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <!--end::Menu-->
        </div>
        <!--end::Aside Menu-->
    </div>
    <!--end::Aside menu-->
</div>
<!--end::Aside-->
