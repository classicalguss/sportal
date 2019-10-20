<form role="form" method="GET" action="{{route('recursive.create', $ids)}}">
    <input type="hidden" name="vid" value="{{ $vid }}">
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">@lang('common.dates')</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" name="daterange" value="{{$dates[0]}} - {{$dates[1]}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">@lang('common.days')</label>
                    <div class="form-group">
                        @foreach(\App\VenueAvailability::$availability_days AS $key => $val)
                            @php $checked = isset($days[$key]) ? "checked" : ""; @endphp
                            <div class="col-md-1">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="day-{{$key}}" {{ $checked }}>{{ $val }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary pull-right flip">@lang('reservation.check-availability')</button>
            </div>
        </div>
    </div>
</form>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/daterangepicker-bs3.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                format: 'YYYY-MM-DD',
                startDate: "{{$dates[0]}}",
                endDate: "{{$dates[1]}}",
                @if(app()->isLocale('ar'))
                locale: {
                    "format": "YYYY-MM-DD",
                    "separator": " - ",
                    "applyLabel": "تطبيق",
                    "cancelLabel": "إلغاء",
                    "fromLabel": "من",
                    "toLabel": "إلى",
                    "customRangeLabel": "Custom",
                    "weekLabel": "اسبوع",
                    "daysOfWeek": [
                        "ح", "ن", "ث", "ع", "خ", "ج", "س"
                    ],
                    "monthNames": [
                        "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"
                    ],
                    "firstDay": 6
                },
                @endif
            });
        });
    </script>
@endpush
