<div class="box-body">

    @include('common.show-errors')

    @php $multi_venue = ($venue->kind == \App\Venue::VENUEKIND_MULTIPLE) ? 'disabled' : ''; @endphp

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name_ar">@lang('common.name-ar')</label>
                <input type="text" class="form-control" id="name_ar" name="name_ar" disabled value="{{ $venue->name_ar or old('name_ar') }}" placeholder="@lang('common.name-ar')">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="name_en">@lang('common.name-en')</label>
                <input type="text" class="form-control" id="name_en" name="name_en" disabled value="{{ $venue->name_en or old('name_en') }}" placeholder="@lang('common.name-en')">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="city">@lang('common.city')</label>
                <select class="form-control" id="city" name="city" {{ $multi_venue }}>
                    @php
                        $city_id = isset($venue) ? \App\Hashes\CityIdHash::public($venue->city_id) : old('city') ?? 0;
                    @endphp
                    @foreach(App\City::all() AS $city)
                        <option value="{{ $city->publicId() }}" {{ $city_id === $city->publicId() ? 'selected' : '' }}>{{ $city->name() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="marker">@lang('common.marker')</label>
                <select class="form-control" id="marker" name="marker" {{ $multi_venue }}>
                    @php
                        $marker_id = isset($venue) ? \App\Hashes\MarkerIdHash::public($venue->marker_id) : old('marker') ?? 0;
                    @endphp
                    @foreach($markers AS $marker)
                        <option value="{{ $marker->publicId() }}" {{ $marker_id === $marker->publicId() ? 'selected' : '' }}>{{ $marker->name() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="address_ar">@lang('common.address-ar')</label>
                <input type="text" class="form-control" id="address_ar" name="address_ar" {{ $multi_venue }} value="{{ $venue->address_ar or old('address_ar') }}" placeholder="@lang('common.address-ar')">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="address_en">@lang('common.address-en')</label>
                <input type="text" class="form-control" id="address_en" name="address_en" {{ $multi_venue }} value="{{ $venue->address_en or old('address_en') }}" placeholder="@lang('common.address-en')">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="types">@lang('common.types')</label>
                <select style="width: 100%" id="types" class="form-control js-example-basic-multiple" name="types[]" multiple="multiple">
                    @php $venue_types = $venue_types ?? old('types') @endphp
                    @foreach(\App\Type::all() AS $type)
                        @php $selected = ''; @endphp
                        @if($venue_types)
                            @foreach($venue_types AS $venue_type)
                                @if($venue_type->id == $type->id)
                                    @php $selected = 'selected'; @endphp
                                @endif
                            @endforeach
                        @endif
                        <option value="{{ $type->publicId() }}" {{ $selected }}>{{ $type->name() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="indoor">@lang('venue.indoor')</label>
                <select class="form-control" id="indoor" name="indoor" {{ $multi_venue }}>
                    @php
                        $indoor = isset($venue) ? $venue->indoor : old('indoor', 0);
                    @endphp
                    <option value="0" {{ $indoor == 0 ? 'selected' : '' }}>@lang('venue.outdoor')</option>
                    <option value="1" {{ $indoor == 1 ? 'selected' : '' }}>@lang('venue.indoor')</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="price">@lang('common.price')</label>
                <div class="input-group">
                    <input type="number" id="price" name="price" value="{{ $venue->price or old('price') }}" class="form-control" placeholder="@lang('common.price')">
                    <span class="input-group-addon"><strong>@lang('common.per-hour')</strong></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="max_players">@lang('common.max-players')</label>
                <div class="input-group">
                    <input type="number" id="max_players" name="max_players" value="{{ $venue->max_players or old('max_players') }}"  class="form-control" placeholder="@lang('common.max-players')">
                    <span class="input-group-addon"><strong>@lang('common.players')</strong></span>
                </div>
            </div>
        </div>
    </div>
    @if($venue->kind == \App\Venue::VENUEKIND_MULTIPLE)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="venues">@lang('common.venues')</label>
                <select style="width: 100%" id="venues" disabled class="form-control multi-venue" name="venues[]" multiple="multiple">
                    @foreach($venue->venues()->get() AS $venue)
                        <option value="{{ $venue->publicId() }}" {{ 'selected' }}>{{ $venue->name() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="rules">@lang('common.rules')</label>
                <textarea class="form-control" id="rules" name="rules" rows="4">{{ $venue->rules or old('rules') }}</textarea>
            </div>
        </div>
    </div>
</div><!-- /.box-body -->

@push('styles')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }} ">
@endpush

@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            theme: 'classic'
        });
        $('.multi-venue').select2({
            theme: 'classic'
        });
    });
</script>
@endpush

