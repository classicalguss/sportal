<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="c96HlpVn5sCFIAKPmS5XwwImEDsHoZWuJjBBU-Ph0wU" />

    <title>@lang('app.name')</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    @if(app()->isLocale('ar'))
        <link rel="stylesheet" href="{{ asset('css/AdminLTE.min.ar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.ar.css') }} ">
    @else
        <link rel="stylesheet" href="{{ asset('css/AdminLTE.min.en.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.en.css') }} ">
    @endif

    <link rel="stylesheet" href="{{ asset('css/skin-green.min.css') }} ">
    @stack('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="skin-green sidebar-mini">
<div class="wrapper">
<!-- Header -->
@include('layouts.header')

<!-- Sidebar -->
@include('layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{ $page_title or __('app.dashboard') }}
            <small>{{ $page_description or null }}</small>
        </h1>
        <!-- You can dynamically generate breadcrumbs here -->
        <ol class="breadcrumb">
            <li><a href="{{  route('dashboard') }}"><i class="fa fa-dashboard"></i> @lang('app.dashboard')</a></li>
             <li class="active">{{ $page_title or __('app.dashboard') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Your Page Content Here -->
        @yield('content')
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Footer -->
@include('layouts.footer')

</div>

<!-- Scripts -->
<script src="{{ asset('js/jQuery-2.1.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@if(app()->isLocale('ar'))
    <script src="{{ asset('js/app.min.ar.js') }}"></script>
@else
    <script src="{{ asset('js/app.min.en.js') }}"></script>
@endif
@stack('scripts')

</body>
</html>
