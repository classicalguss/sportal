@extends('layouts.app')

@section('content')
    @include('common.show-message')

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <div id='calendar' style="max-width: 1272px;margin: 0 auto;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-title" style='left:10px'>
                        @include('reservation.include.filter-list')
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody><tr>
                            <th>@lang('common.facility')</th>
                            <th>@lang('common.venue')</th>
                            <th>@lang('common.date')</th>
                            <th>@lang('common.time')</th>
                            <th>@lang('common.status')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($availabilities AS $availability)
                            <tr>
                                <td>{{ $availability->facility()->name() }}</td>
                                <td>{{ $availability->venue()->name() }}</td>
                                <td>{{ $availability->date }}</td>
                                <td>{!! $availability->time() !!}</td>
                                <td>
                                    @if($availability->status == \App\VenueAvailability::AVAILABILITYSTATUS_AVAILABLE)
                                        <span class="label label-success">@lang('availability.status-available')</span>
                                    @elseif($availability->status == \App\VenueAvailability::AVAILABILITYSTATUS_RESERVED)
                                        <span class="label label-danger">@lang('availability.status-reserved')</span>
                                    @endif
                                </td>
                                <td>
                                    <form role="form">
                                        <div class="btn-group">
                                            @php $parameters = $availability->publicId() . '?vid=' . $vid @endphp
                                            <a href="{{ route('reservations.create', $parameters) }}" class="btn btn-primary btn-sm">@lang('common.book')</a>
                                            <a href="{{ route('recursive.create', $parameters) }}" class="btn btn-warning btn-sm"><i class="fa fa-undo"></i></a>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">@lang('common.no-results')</td>
                            </tr>
                        @endforelse
                        </tbody></table>
                </div>
            </div>
                {{ $availabilities->appends(['venue' => request('venue'), 'interval_time' => request('interval_time'), 'date' => request('date'), 'time' => request('time')])->links() }}
        </div>
    </div>

@endsection