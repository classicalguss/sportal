<form class="form-inline">
    <select class="form-control input-sm" id="venue" name="venue" >
        @php $venue_id = request('venue') ?? $venues[0]->publicId(); @endphp
        @foreach($venues AS $venue)
            <option value="{{ $venue->publicId() }}" {{ $venue_id == $venue->publicId() ? 'selected' : '' }}>{{ $venue->name() }}</option>
        @endforeach
    </select>
    @if($interval_enable == 1)
    <select class="form-control input-sm" id="interval_time" name="interval_time" >
        @php $selected_interval_time = request('interval_time') ?? $interval_times[0]; @endphp
        @foreach($interval_times AS $interval_time)
            <option value="{{ $interval_time }}" {{ $selected_interval_time == $interval_time ? 'selected' : '' }}>{{ $interval_time }}</option>
        @endforeach
    </select>
    @endif
    <div class="form-group">
        <input type="text" name="date" class="form-control input-sm" id="datepicker" readonly="true" data-date-format="yyyy-mm-dd" value="{{ request('date', $date_default) }}" placeholder="@lang('common.select-date')">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-sm" id="filter_btn">@lang('common.filter')</button>
    </div>
    <div class="form-group">
        <a href="{{ route('reservations.list') }}" class="btn btn-default btn-sm">@lang('common.reset')</a>
    </div>
</form>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }} ">
    <style>
        #datepicker{ background-color: #fff !important; opacity: 1; }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script type="application/javascript">
        //Date picker
        $.fn.datepicker.dates['ar'] = {
            days: ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت", "الأحد"],
            daysShort: ["أحد", "اثنين", "ثلاثاء", "أربعاء", "خميس", "جمعة", "سبت", "أحد"],
            daysMin: ["ح", "ن", "ث", "ع", "خ", "ج", "س", "ح"],
            months: ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"],
            monthsShort: ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"],
            today: "اليوم",
            rtl: true
        };
        $('#datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            ignoreReadonly: true,
            allowInputToggle: true,
            @if(app()->isLocale('ar'))
            language: 'ar'
            @endif
        });
        (() => {
            const venue = document.querySelector('#venue');
            const interval_time = document.querySelector('#interval_time');
            const filter = document.querySelector('#filter_btn');
            const handleVenue = () => {
                var d = new Date();
                $('#datepicker').datepicker('update', '"'+d.getDate()+'"');
                if(interval_time) {
                    interval_time.remove();
                }
                filter.click();
            };
            const handleIntervalTime = () => {
                filter.click();
            };

            venue.addEventListener('change', handleVenue);
            if(interval_time) {
                interval_time.addEventListener('change', handleIntervalTime);
            }
        })();
    </script>
@endpush
