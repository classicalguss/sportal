<form role="form" action="{{ route('markers.store') }}" method="POST">
    {{ csrf_field() }}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('common.create-marker')</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <input type="hidden" name="facility" value="{{ $facility->publicId() }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name_ar">@lang('common.name-ar')</label>
                        <input required type="text" name="name_ar" class="form-control" placeholder="@lang('common.name-ar')">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name_en">@lang('common.name-en')</label>
                        <input required type="text" id="name_en" name="name_en" class="form-control" placeholder="@lang('common.name-en')">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="longitude">@lang('common.longitude')</label>
                        <input required type="text" pattern="-?\d{1,3}\.\d+" name="longitude" class="form-control" placeholder="@lang('common.longitude')">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="latitude">@lang('common.latitude')</label>
                        <input required type="text" pattern="-?\d{1,3}\.\d+" name="latitude" class="form-control" placeholder="@lang('common.latitude')">
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">@lang('common.create-marker')</button>
        </div>
    </div>
</form>
