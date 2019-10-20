<section class="invoice">
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-center">{{ $venue->name() }}</h2>

            <div class="table-responsive margin-bottom">
                <table class="table">
                    <tbody><tr>
                        <th style="width: 50%" class="text-right flip">@lang('common.facility')</th>
                        <td class="text-left flip">{{ $venue->facilityName() }}</td>
                    </tr><tr>
                        <th style="width: 50%" class="text-right flip">@lang('common.types')</th>
                        <td class="text-left flip">{!! $venue->typesName() !!}</td>
                    </tr><tr>
                        <th style="width: 50%" class="text-right flip">@lang('common.indoor')</th>
                        <td class="text-left flip">
                            @if($venue->indoor == 1)
                                <span class="label label-success">@lang('venue.indoor')</span>
                            @elseif($venue->indoor == 0)
                                <span class="label label-primary">@lang('venue.outdoor')</span>
                            @endif
                        </td>
                    </tr><tr>
                        <th style="width: 50%" class="text-right flip">@lang('common.city')</th>
                        <td class="text-left flip">{{ $venue->cityName() }}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.max-players')</th>
                        <td class="text-left flip">{{ $venue->max_players }}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.date')</th>
                        <td class="text-left flip">{{ isset($reservation) ? $reservation->date() : $venue_availability->date }}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.time')</th>
                        <td class="text-left flip">{!! isset($reservation) ? $reservation->time() : $venue_availability->time() !!}</td>
                    </tr>
                    <tr>
                        <th class="text-right flip">@lang('common.price')</th>
                        <td class="text-left flip">{{ isset($reservation) ? $reservation->price : $venue_availability->price }}<strong>@lang('common.jd')</strong></td>
                    </tr></tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row margin-bottom">
                <div class="col-md-offset-3 col-md-6">
                    @include('common.images-slider')
                </div>
            </div>
            <div class="row">
                <h3 class="text-center">@lang('common.rules')</h3>
                <div class="text-left flip">{{ $venue_availability->venue()->rules }}</div>
            </div>
        </div>
    </div>
</section>
