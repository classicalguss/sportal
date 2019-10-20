<section class="invoice">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="info-box {{ $reservation->statusBGColor() }}">
                <span class="info-box-icon"><i class="fa {{ $reservation->statusIcon() }}"></i></span>
                <div class="info-box-content">
                    <h2>{{ $reservation->statusName() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="info-box bg-maroon">
                <span class="info-box-icon"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('common.booked-by')</span>
                    <span class="info-box-number">{{ $reservation->reserverName() }}</span>
                    <span class="info-box-more">{{ $reservation->type()->name() }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="info-box bg-blue-active">
                <span class="info-box-icon"><i class="fa fa-user"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('common.info')</span>
                    @if(isset($reservation->customer()->name))
                        <span class="info-box-number">{{ $reservation->customer()->name }}</span>
                    @endif
                    @if(isset($reservation->customer()->address)))
                        <span class="info-box-more">{{ $reservation->customer()->address }}</span>
                    @else
                        <span class="info-box-more">Amman Jordan</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="info-box bg-gray">
                <span class="info-box-icon"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('common.contact')</span>
                    <span class="info-box-number">{{ $reservation->customer()->phone_number }}</span>
                    @if(isset($reservation->customer()->email))
                        <span class="info-box-more">{{ $reservation->customer()->email }}</span>
                    @endif
                </div>
            </div>
        </div>
        @if($reservation->notes != null)
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-sticky-note-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('common.notes')</span>
                    <p>{{ $reservation->notes }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
