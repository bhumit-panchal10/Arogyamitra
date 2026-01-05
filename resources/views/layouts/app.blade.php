<!DOCTYPE html>

<html lang="en">

<head>
	@include('partials.head')
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">

	<div class="d-flex flex-column flex-root">
		<!--begin::Page-->
		<div class="page d-flex flex-row flex-column-fluid">
			<!--begin::Aside-->
			@include('partials.sidebar')
			<!--end::Aside-->

			<!--begin::Wrapper-->
			<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
				<!--begin::Header-->
				@include('partials.header')
				<!--end::Header-->

				<!--begin::Content-->
				<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
					<!--begin::Toolbar-->
					<div class="toolbar" id="kt_toolbar">
						<!--begin::Container-->
						<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
							<!--begin::Page title-->
							<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
								@include('partials.breadcrumbs')
							</div>
							@php
							$routeArr = ['home','report','edit','show','prant','vibhag','jilla','taluka','gramjuth','grams'];
							@endphp
							@if(Request::segment(1) != 'home' && Request::segment(1) != 'medicineRequest' && Request::segment(1) != 'profile' && Request::segment(1) != 'password' && Request::segment(1) != 'medicine-stock' && Request::segment(1) != 'report')
								@if(!Request::is(Request::segment(1).'/*'))
									@if(!in_array(Request::segment(1).'/create', $routeArr) && !in_array(Request::segment(1).'/*', $routeArr) && Request::segment(2) != 'create' && !in_array(Request::segment(3), $routeArr))
									<div class="card-toolbar">
										<div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
												@if (Request::segment(1) == 'users')
													<a href="{{ route('user-export') }}" class="btn btn-info me-5">Full Export</a>
												@endif
												@if(Auth::user()->role == '1' || Auth::user()->role == '4')
													<a href="{{route(Request::segment(1).'.create')}}" class="btn btn-xs btn-primary  me-5">{{ trans('messages.users.fields.create') }}</a>
												@endif
											</div>
										</div>
									@endif
								@endif
							@elseif (Request::segment(1) == 'medicine-stock')
								<div class="card-toolbar">
									<div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
										<a href="javascript:void(0);" class="btn btn-xs btn-primary" id="stockUpdate">Submit</a>
									</div>
								</div>
								@elseif (Request::segment(1) == 'report' && Request::segment(2) == 'stock-report-show')
									<div class="card-toolbar">
										<div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
											<a href="{{ route('report.backend') }}" class="btn btn-xs btn-primary">Back</a>
										</div>
									</div>
								@elseif (Request::segment(1) == 'report' && Request::segment(2) == 'stockiest-report-show')
									<div class="card-toolbar">
										<div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
										<a href="{{ route('report.stockiest') }}" class="btn btn-xs btn-primary">Back</a>
										</div>
									</div>
								@elseif (Request::segment(1) == 'report' && Request::segment(2) == 'appUsers-report-show')
									<div class="card-toolbar">
										<div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
										@php
											$selectedGram = $selectedGram ?? null;
										@endphp
										<a href="{{ route('report.appUsers') }}" class="btn btn-xs btn-primary">Back</a>
										</div>
									</div>
								@endif
						</div>
					</div>
					<!--begin::Post-->
					<div class="post d-flex flex-column-fluid" id="kt_post">
						<!--begin::Container-->
						<div id="kt_content_container" class="container-xxl">
							<!--begin::Row-->
							@yield('content')
							<!--end::Row-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Post-->
				</div>
				<!--end::Content-->

				<!--begin::Footer-->
				@include('partials.footer')
				<!--end::Footer-->
			</div>
			<!--end::Wrapper-->

		</div>
		<!--end::Page-->
	</div>

	@include('partials.javascripts')
</body>

</html>