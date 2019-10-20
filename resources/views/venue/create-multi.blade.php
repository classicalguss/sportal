<div class="modal fade" id="create-multi-venue">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="POST" action="{{route('venues.storeMulti')}}">
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
                                        $type_id = old('type', 0)
                                    @endphp
                                    <option value="0" {{ $type_id === 0 ? 'selected' : '' }}>@lang('common.select-type')</option>
                                    @foreach(App\Type::all() AS $type)
                                        <option value="{{ $type->publicId() }}" {{ $type_id === $type->publicId() ? 'selected' : '' }}>{{ $type->name() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="venues">@lang('common.venues')</label>
                                <select style="width: 100%" id="venues" class="form-control multi-venue" name="venues[]" multiple="multiple">
                                    @foreach($facility->venues()->where('kind', \App\Venue::VENUEKIND_SINGLE)->get() AS $venue)
                                        @php $selected = in_array($venue->publicId(), old('venues', [])) ? 'selected' : ''; @endphp
                                        <option value="{{ $venue->publicId() }}" {{ $selected }}>{{ $venue->name() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left flip" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-warning">@lang('venue.create-multi')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }} ">
@endpush

@push('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.multi-venue').select2({
                theme: 'classic'
            });
        });
    </script>
@endpush