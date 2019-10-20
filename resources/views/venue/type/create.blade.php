<div class="modal fade" id="create-type">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="POST" enctype="multipart/form-data" action="{{route('types.store')}}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="box-title">@lang('type.create')</h3>
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
                                <label for="color">@lang('common.color')</label>
                                <input type="text" class="form-control my-colorpicker1" id="color" name="color" value="{{ old('color') }}" placeholder="@lang('common.color')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">@lang('common.image')</label>
                                <input type="file" class="form-control" id="image" name="image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left flip" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('type.create')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-colorpicker.min.css')}}">
@endpush

@push('scripts')
    <script src="{{ asset('js/bootstrap-colorpicker.min.js') }}"></script>
    <script>
        $('.my-colorpicker1').colorpicker()
    </script>
@endpush