@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('dash.chart-venues')</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <canvas id="pieChart" style="height: 393px; width: 787px;" height="393" width="787"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('dash.number-of-reservations')</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <canvas id="lineChart" style="height: 393px; width: 787px;" height="393" width="787"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">@lang('dash.reservations-for-today')</div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>@lang('common.name')</th>
                            <th>@lang('common.phone-number')</th>
                            <th>@lang('common.venue')</th>
                            <th>@lang('common.time')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($todaysReservations AS $reservation)
                            <tr>
                                <td>{{ $reservation->customer->name ?? '' }}</td>
                                <td>{{ $reservation->customer->phone_number ?? '' }}</td>
                                <td>{{ $reservation->venue->name() }}</td>
                                <td>{!! $reservation->time() !!}</td>
                                <td>
                                    <a href="{{ route('reservations.show', $reservation->publicId()) }}"
                                       class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">@lang('common.no-results')</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">@lang('dash.reservations-for-tomorrow')</div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>@lang('common.name')</th>
                            <th>@lang('common.phone-number')</th>
                            <th>@lang('common.venue')</th>
                            <th>@lang('common.time')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($tomorrowsReservations AS $reservation)
                            <tr>
                                <td>{{ $reservation->customer->name ?? '' }}</td>
                                <td>{{ $reservation->customer->phone_number ?? '' }}</td>
                                <td>{{ $reservation->venue->name() }}</td>
                                <td>{!! $reservation->time() !!}</td>
                                <td>
                                    <a href="{{ route('reservations.show', $reservation->publicId()) }}"
                                       class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">@lang('common.no-results')</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        var lang = '{{ Config::get('app.locale') }}';
        var pie = document.getElementById('pieChart').getContext('2d');
        var reservationsData = @json($reservations);
        var labels = [];
        var durations = [];
        for (var i in reservationsData) {
            labels.push(lang == 'en' ? reservationsData[i].name_en : reservationsData[i].name_ar);
            durations.push(reservationsData[i].duration);
        }
        var chart = new Chart(pie, {
            // The type of chart we want to create
            type: 'doughnut',

            // The data for our dataset
            data: {
                labels: labels,
                datasets: [{
                    data: durations,
                    backgroundColor: ['red', 'blue', 'green', 'yellow', 'orange', 'violet', 'cyan', 'pink', 'purple']
                }]
            },
            options: {
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var meta = dataset._meta[Object.keys(dataset._meta)[0]];
                            var total = meta.total;
                            var currentValue = dataset.data[tooltipItem.index];
                            var percentage = parseFloat((currentValue / total * 100).toFixed(1));
                            return percentage + '%';
                        },
                        title: function (tooltipItem, data) {
                            return data.labels[tooltipItem[0].index];
                        }
                    }
                }
            }
        });
        var timeline = document.getElementById('lineChart').getContext('2d');
        var reservationsTimeLineData = @json($daysReservationsCount);
        var dateFormat = 'MMMM DD YYYY';
        var date = moment('April 01 2017', dateFormat);
        var date2 = moment('April 02 2017', dateFormat);
        var reservationsTimeLine = [];
        for (var i in reservationsTimeLineData) {
            reservationsTimeLine.push({
                x: reservationsTimeLineData[i].date,
                y: reservationsTimeLineData[i].reservations
            })
        }
        var chart = new Chart(timeline, {
            type: 'line',
            data: {
                datasets: [{
                    data: reservationsTimeLine,
                    label: '@lang('dash.number-of-reservations')',
                    fill: false,
                    backgroundColor: 'red',
                    borderColor: 'red',
                    lineTension: 0.1
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        type: 'time',
                        distribution: 'series',
                        ticks: {
                            source: 'auto',
                        }
                    }],
                },
            }
        });
    </script>
@endsection
