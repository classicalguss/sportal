<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@lang('app.name')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap and Font Awesome css-->
    <link rel="stylesheet" href="{{ asset('soon/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('soon/css/bootstrap.min.css') }}">
    <!-- Google fonts-->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Pacifico">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">
    <!-- Theme stylesheet-->
    <link rel="stylesheet" href="{{ asset('soon/css/style.default.css') }}" id="theme-stylesheet">
    <!-- Favicon-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<body>
    <div style="background-image: url('{{ asset("soon/img/bg-playground.jpg") }}')" class="main">
    <div class="overlay"></div>
    <div class="container">
        <p class="social">
            <a href="https://www.facebook.com/sportal.jo/" title="Sportal App - Facebook" class="facebook"><i class="fa fa-facebook"></i></a>
            <a href="https://twitter.com/Sportal_Jo" title="Sportal App - Twitter" class="twitter"><i class="fa fa-twitter"></i></a>
            <a href="https://www.instagram.com/sportal.jo/" title="Sportal App - Instagram" class="instagram"><i class="fa fa-instagram"></i></a>
            @if (Route::has('login'))
                @if (Auth::check())
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endif
            @endif
        </p>
        <h1 class="cursive">
            <img class="center-block" src="{{ asset('soon/img/logo-text.png') }}">
        </h1>
        <h2 class="sub">One Plays All</h2>
        <div class="container">
            <div class="row">
                <div class="col-md-offset-3 col-md-3 col-sm-12">
                    <a href="https://play.google.com/store/apps/details?id=sportal.com.sportal" target="_blank">
                        <img class="img-responsive" src="{{ asset('soon/img/store-play.png') }}">
                    </a>
                </div>
                <div class="col-md-3 col-sm-12">
                    <a href="https://itunes.apple.com/us/app/sportal-app/id1359254879?mt=8" target="_blank">
                        <img class="img-responsive" src="{{ asset('soon/img/store-app.png') }}">
                    </a>
                </div>
            </div>
        </div>
        </div>
            <div class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <p>&copy;{{ date("Y") }} Sportal</p>
                        </div>
                        <div class="col-md-6">
                            <p class="credit">info@sportal-app.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- JAVASCRIPT FILES -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="javascripts/vendor/jquery-1.11.0.min.js"><\/script>')</script>
    <script src="{{ asset('soon/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('soon/js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('soon/js/front.js') }}"></script>
</body>
</html>