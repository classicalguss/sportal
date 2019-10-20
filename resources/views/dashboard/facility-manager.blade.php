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
                    <h3 class="box-title">Number of reservations</h3>

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
    <script>
        var pie = document.getElementById('pieChart').getContext('2d');
        var reservationsData = @json($reservations);
        var labels = [];
        var durations = [];
        for (var i in reservationsData) {
           labels.push(reservationsData[i].name_en);
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
                    label: '# of reservations',
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
