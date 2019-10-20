@extends('layouts.app')

@section('content')
    <!-- Your Page Content Here -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-title" style='left:10px'>
                        <form class="form-inline">
                            <div class="form-group">
                                <input list="facilities" type="text" name="facility" class="form-control input-sm" placeholder="@lang('common.facility')" value="{{ request('facility') }}">
                                <datalist id="facilities">
                                    @foreach($facilities AS $facility)
                                        <option value="{{ $facility->name() }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="form-group">
                                <input list="venues" type="text" name="venue" class="form-control input-sm" placeholder="@lang('common.venue')" value="{{ request('venue') }}">
                                <datalist id="venues">
                                    @foreach($venues AS $venue)
                                        <option value="{{ $venue->name() }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="form-group">
                                <input type="text" name="phone_number" class="form-control input-sm" placeholder="@lang('common.phone-number')" value="{{ request('phone_number') }}">
                            </div>
                            <select class="form-control input-sm" id="reserver" name="reserver" >
                                @php $selected_reserver = request('reserver') ?? '0'; @endphp
                                <option value="0" {{ $selected_reserver == '0' ? 'selected' : '' }}>@lang('reservation.all-reserver')</option>
                                @foreach(\App\Reservation::$reserver AS $reserver_id => $reserver_name)
                                    <option value="{{ $reserver_id }}" {{ $selected_reserver == $reserver_id ? 'selected' : '' }}>{{ \App\Reservation::reserverId($reserver_id) }}</option>
                                @endforeach
                            </select>
                            <div class="form-group">
                                <input type="text" name="date" class="form-control input-sm" id="datepicker" readonly="true" data-date-format="yyyy-mm-dd" value="{{ request('date') }}" placeholder="@lang('common.select-date')">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm">@lang('common.filter')</button>
                            </div>
                            <div class="form-group">
                                <a href="{{ route('reservations.index') }}" class="btn btn-default btn-sm">@lang('common.reset')</a>
                            </div>
                        </form>
                    </div>
                    <div class="box-tools">
                        <a href="{{ route('reservations.list') }}" class="btn btn-success btn-bg">@lang('reservation.create')</a>
                    </div>
                </div><!-- /.box-header -->
                @include('reservation.include.list-reservations')
            </div><!-- /.box -->
            {{ $reservations->appends(['facility' => request('facility'), 'venue' => request('venue'), 'user' => request('user'), 'phone_number' => request('phone_number'), 'reserver' => request('reserver'), 'date' => request('date')])->links() }}
        </div>
    </div>
@endsection


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }} ">
    <style>
        #datepicker{ background-color: #fff !important; opacity: 1; }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
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

        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
