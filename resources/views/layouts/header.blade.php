<!-- Main Header -->
<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="logo">
        <span class="logo-mini"><b><i class="fa fa-soccer-ball-o"></i></b></span>
        <span class="logo-lg">Sportal-App</span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Switch Locale -->
                <li>
                    <a href="/locale/{{__('app.locale')}}"><img src="{{asset('img/'.__('app.locale').'.png')}}"> @lang('app.locale-text') </a>
                </li>
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ asset('img/user-default.png') }}" class="user-image" alt="User Image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">
                            @if(Auth::check()){{ Auth::user()->name }}@endif
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ asset('img/user-default.png') }}" class="img-circle" alt="User Image">
                            <p>
                                @if(Auth::check()){{ Auth::user()->name }}@endif
                                @role('super_admin')
                                    <small>@lang('role.super_admin')</small>
                                @else
                                    <small>@lang('role.facility_manager')</small>
                                @endrole
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                                   class="btn btn-default btn-flat">
                                    @lang('app.logout')
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>