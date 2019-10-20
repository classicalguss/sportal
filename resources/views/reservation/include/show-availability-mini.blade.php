<div class="row">
    <div class="col-md-4">
        <table class="table">
            <tbody><tr>
                <th style="width: 100px">@lang('common.facility')</th>
                <td>{{ $venue_availability->facility()->name() }}</td>
            </tr><tr>
                <th>@lang('common.venue')</th>
                <td>{{ $venue_availability->venue()->name() }}</td>
            </tr><tr>
                <th>@lang('common.city')</th>
                <td>{{ $venue_availability->venue()->cityName() }}</td>
            </tr></tbody>
        </table>
    </div>
    <div class="col-md-4">
        <table class="table">
            <tbody><tr>
                <th style="width: 100px">@lang('common.indoor')</th>
                <td>
                    @if($venue_availability->venue()->indoor == 1)
                        <span class="label label-success">@lang('venue.indoor')</span>
                    @elseif($venue_availability->venue()->indoor == 0)
                        <span class="label label-primary">@lang('venue.outdoor')</span>
                    @endif
                </td>
            </tr><tr>
                <th>@lang('common.max-players')</th>
                <td>{{ $venue_availability->venue()->max_players }}</td>
            </tr></tbody>
        </table>
    </div>
    <div class="col-md-4">
        <table class="table">
            <tbody><tr>
                <th style="width: 100px">@lang('common.time')</th>
                <td>{!! $venue_availability->time() !!}</td>
            </tr><tr>
                <th>@lang('common.price')</th>
                <td>{{ $venue_availability->price }}<strong>@lang('common.jd')</strong></td>
            </tr></tbody>
        </table>
    </div>
</div>
