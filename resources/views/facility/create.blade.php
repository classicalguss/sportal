<div class="modal fade" id="create-facility">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="POST" action="{{route('facilities.store')}}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="box-title">@lang('facility.create')</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_ar">@lang('common.name-ar')</label>
                                <input type="text" class="form-control" id="name_ar" name="name_ar" value="{{ old('name_ar') }}" placeholder="@lang('common.name-ar')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_en">@lang('common.name-en')</label>
                                <input type="text" class="form-control" id="name_en" name="name_en" value="{{ old('name_en') }}" placeholder="@lang('common.name-en')">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">@lang('common.city')</label>
                                <select class="form-control" id="city" name="city" >
                                    @php
                                        $city_id = old('city') ?? 0;
                                    @endphp
                                    <option value="0" {{ $city_id === 0 ? 'selected' : '' }}>@lang('common.select-city')</option>
                                    @foreach(App\City::all() AS $city)
                                        <option value="{{ $city->publicId() }}" {{ $city_id === $city->publicId() ? 'selected' : '' }}>{{ $city->name() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left flip" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('facility.create')</button>
                </div>
            </form>
        </div>
    </div>
</div>
