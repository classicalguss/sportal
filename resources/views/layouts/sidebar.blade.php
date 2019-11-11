<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image flip">
                <img src="{{ asset('img/user-default.png') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info flip">
                <p>{{ Auth::user()->name }}</p>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="header">@lang('app.pages')</li>
            <li class="{{ (Request::is('dashboard') ? 'active' : '') }}"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> <span>@lang('app.dashboard')</span></a></li>
            <li class="{{ (Request::is('reservations/calendar') ? 'active' : '') }}"><a href="{{ route('reservations.calendar') }}"><i class="fa fa-calendar"></i> <span>@lang('app.calendar')</span></a></li>
            @role('super_admin')
            <li class="{{ (Request::is('facilities') ? 'active' : '') }}"><a href="{{ route('facilities.index') }}"><i class="fa fa-building"></i> <span>@lang('app.facilities')</span></a></li>
            @endrole
            @role('facility_manager')
            @if(count(Auth::user()->facilities()) == 1)
                @php $facility = Auth::user()->facility() @endphp
                <li class="{{ (Request::is('facilities/'.$facility->publicId()) ? 'active' : '') }}"><a href="{{ route('facilities.show', $facility->publicId()) }}"><i class="fa fa-building"></i> <span>@lang('facility.manage')</span></a></li>
            @else
                <li class="treeview {{ (Request::is(['facilities', 'facilities/*']) ? 'active' : '') }}">
                    <a href="#"><i class="fa fa-building"></i> <span>@lang('app.facilities')</span></a>
                    <ul class="treeview-menu">
                        @foreach(Auth::user()->facilities() AS $facility)
                            <li class="{{ (Request::is('facilities/'.$facility->publicId()) ? 'active' : '') }}"><a href="{{ route('facilities.show', $facility->publicId()) }}"><i class="fa fa-circle-o"></i> <span>{{ $facility->name() }}</span></a></li>
                        @endforeach
                    </ul>
                </li>
            @endif
            @endrole
            <li class="{{ (Request::is('reservations') ? 'active' : '') }}"><a href="{{ route('reservations.index') }}"><i class="fa fa-table"></i> <span>@lang('app.reservations')</span></a></li>
            <li class="{{ (Request::is('recursive') ? 'active' : '') }}"><a href="{{ route('recursive.index') }}"><i class="fa fa-undo"></i> <span>@lang('app.recursive')</span></a></li>
            <li class="{{ (Request::is('reservations/list') ? 'active' : '') }}"><a href="{{ route('reservations.list') }}"><i class="fa fa-calendar-check-o"></i> <span>@lang('app.reservation-create')</span></a></li>
            @role('super_admin')
            <li class="{{ (Request::is('admins') ? 'active' : '') }}"><a href="{{ route('admins.index') }}"><i class="fa fa-user-secret"></i> <span>@lang('app.admins')</span></a></li>
            <li class="{{ (Request::is('users') ? 'active' : '') }}"><a href="{{ route('users.index') }}"><i class="fa fa-user"></i> <span>@lang('app.users')</span></a></li>
            <li class="{{ (Request::is('types') ? 'active' : '') }}"><a href="{{ route('types.index') }}"><i class="fa fa-file"></i> <span>@lang('app.venue-types')</span></a></li>
            {{--<li class="{{ (Request::is('sms') ? 'active' : '') }}"><a href="{{ route('sms.index') }}"><i class="fa fa-comments"></i> <span>@lang('app.sms-logs')</span></a></li> --}}
            @endrole
        </ul>
    </section>
</aside>
