<div class="box-body">

    @include('common.show-errors')

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name_ar">@lang('common.name-ar')</label>
                <input type="text" class="form-control" id="name_ar" name="name_ar" {{ $name_disabled ?? "" }} value="{{ $facility->name_ar or old('name_ar') }}" placeholder="@lang('common.name-ar')">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="name_en">@lang('common.name-en')</label>
                <input type="text" class="form-control" id="name_en" name="name_en" {{ $name_disabled ?? "" }} value="{{ $facility->name_en or old('name_en') }}" placeholder="@lang('common.name-en')">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="city">@lang('common.city')</label>
                <select class="form-control" id="city" name="city" >
                    @php
                        $city_id = isset($facility) ? \App\Hashes\CityIdHash::public($facility->city_id) : old('city') ?? 0;
                    @endphp
                    <option value="0" {{ $city_id === 0 ? 'selected' : '' }}>@lang('common.select-city')</option>
                    @foreach(App\City::all() AS $city)
                        <option value="{{ $city->publicId() }}" {{ $city_id === $city->publicId() ? 'selected' : '' }}>{{ $city->name() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($marker_enabled)
        <div class="col-md-6">
            <div class="form-group">
                <label for="marker">@lang('common.marker')</label>
                <select class="form-control" id="marker" name="marker">
                    @php
                        $marker_id = isset($facility) ? \App\Hashes\MarkerIdHash::public($facility->marker_id) : old('marker') ?? 0;
                    @endphp
                    <option value="0">@lang('common.select-marker')</option>
                    @foreach($markers AS $marker)
                        <option value="{{ $marker->publicId() }}" {{ $marker_id === $marker->publicId() ? 'selected' : '' }}>{{ $marker->name() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="address_ar">@lang('common.address-ar')</label>
                <input type="text" class="form-control" id="address_ar" name="address_ar" value="{{ $facility->address_ar or old('address_ar') }}" placeholder="@lang('common.address-ar')">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="address_en">@lang('common.address-en')</label>
                <input type="text" class="form-control" id="address_en" name="address_en" value="{{ $facility->address_en or old('address_en') }}" placeholder="@lang('common.address-en')">
            </div>
        </div>
    </div>
</div><!-- /.box-body -->
