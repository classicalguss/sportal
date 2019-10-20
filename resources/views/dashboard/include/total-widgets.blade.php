<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $total['facilities'] }}</h3>
                <p>@lang('dash.total-facilities')</p>
            </div>
            <div class="icon">
                <i class="ion ion-location"></i>
            </div>
            <div class="small-box-footer"> <i class="fa"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $total['venues'] }}</h3>
                <p>@lang('dash.total-venues')</p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-football"></i>
            </div>
            <div class="small-box-footer"> <i class="fa"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $total['users'] }}</h3>
                <p>@lang('dash.total-users')</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <div class="small-box-footer"> <i class="fa"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>{{ $total['reservations'] }}</h3>
                <p>@lang('dash.total-reservations')</p>
            </div>
            <div class="icon">
                <i class="ion ion-thumbsup"></i>
            </div>
            <div class="small-box-footer"> <i class="fa"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-blue-active">
            <div class="inner">
                <h3>{{ $total['sms'] }}</h3>
                <p>@lang('dash.total-sms')</p>
            </div>
            <div class="icon">
                <i class="ion ion-android-textsms"></i>
            </div>
            <div class="small-box-footer"> <i class="fa"></i></div>
        </div>
    </div>
</div>