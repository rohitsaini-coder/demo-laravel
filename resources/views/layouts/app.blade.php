<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ config('app.name') }} | @yield('title')</title>
    @include('partials.hscript')
    @yield('custom_css')

</head>
<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')


        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">
				    @yield('main-content')

                </div>
            </div>

            @include('partials.footer')

        </div>

    </div>
	<div class="popup_render_div"></div>

    @include('partials.fscript')


    @include('partials.alert')

	@yield('custom_JS')
</body>
</html>
