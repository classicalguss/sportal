<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="phone_number">@lang('common.phone-number') *</label>
            <div class="input-group date">
                @if(app()->isLocale('en'))<div class="input-group-addon">962</div>@endif
                <input style="direction: ltr" type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="ex: 791234567">
                @if(app()->isLocale('ar'))<div class="input-group-addon">962</div>@endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">@lang('common.name') *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="@lang('common.name')">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="type">@lang('common.type') *</label>
            <select class="form-control" id="type" name="type" >
                @php $type_id = old('type') ?? 0; @endphp
                @foreach($venue_types AS $type)
                    <option value="{{ $type->publicId() }}" {{ $type_id === $type->publicId() ? 'selected' : '' }}>{{ $type->name() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="price">@lang('common.price')</label>
            <div class="input-group">
                <input type="text" id="price" name="price" value="{{ old('price', $venue_availability->price)  }}" class="form-control" placeholder="@lang('common.price')">
                <span class="input-group-addon"><strong>@lang('common.jordanian-dinar')</strong></span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="reservation_type">@lang('common.reservation-type')</label>
            <select class="form-control" id="reservation_type" name="reservation_type" >
                @php $reservation_type_id = old('reservation_type', 1); @endphp
                @php $reservation_types = \App\ReservationType::all(); @endphp
                @foreach($reservation_types AS $reservation_type)
                    <option value="{{ $reservation_type->id }}" {{ $reservation_type_id === $reservation_type->id ? 'selected' : '' }}>{{ $reservation_type->name() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">@lang('common.email')</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="@lang('common.email')">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="address">@lang('common.address')</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" placeholder="@lang('common.address')">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="notes">@lang('common.notes')</label>
            <textarea id="notes" name="notes" class="form-control" rows="1"></textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <small>*: @lang('common.required-fields')</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <button type="submit" class="btn btn-primary pull-right flip">@lang('reservation.create')</button>
        </div>
    </div>
</div>