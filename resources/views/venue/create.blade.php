<div class="modal fade" id="create-venue">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="POST" action="{{route('venues.store')}}">
                {{ csrf_field() }}
                <input type="hidden" name="facility" value="{{ $facility->publicId() }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="box-title">@lang('venue.create') <small>{{ $facility->name() }}</small></h3>
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
                                <label for="type">@lang('common.type')</label>
                                <select class="form-control" id="type" name="type" >
                                    @php
                                        $type_id = isset($type_id) ? \App\Hashes\TypeIdHash::public($venue->type_id) : old('type') ?? 0;
                                    @endphp
                                    <option value="0" {{ $type_id === 0 ? 'selected' : '' }}>@lang('common.select-type')</option>
                                    @foreach(App\Type::all() AS $type)
                                        <option value="{{ $type->publicId() }}" {{ $type_id === $type->publicId() ? 'selected' : '' }}>{{ $type->name() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left flip" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-success">@lang('venue.create')</button>
                </div>
            </form>
        </div>
    </div>
</div>
