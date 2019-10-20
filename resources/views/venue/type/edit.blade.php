@extends('layouts.app')

@section('content')

    @include('common.show-message')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('type.update')</h3>
        </div>
        <form role="form" method="POST" enctype="multipart/form-data" action="{{route('types.update', $type->publicId())}}">
            <input type="hidden" value="PATCH" name="_method">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_ar">@lang('common.name-ar')</label>
                            <input type="text" class="form-control" id="name_ar" name="name_ar" value="{{ $type->name_ar }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_en">@lang('common.name-en')</label>
                            <input type="text" class="form-control" id="name_en" name="name_en" value="{{ $type->name_en }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="color">@lang('common.color')</label>
                            <input type="text" class="form-control my-colorpicker1" id="color" name="color" value="{{ $type->color }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="image">@lang('common.image')</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="">
                                <img ID="imageHolder" class="img-responsive center-block" style="width: 90px;height: auto;" src="{{ $type->imageFileName() }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">@lang('type.update')</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-colorpicker.min.css')}}">
@endpush

@push('scripts')
    <script src="{{ asset('js/bootstrap-colorpicker.min.js') }}"></script>
    <script type="application/javascript">
        $('.my-colorpicker1').colorpicker()
    </script>

    <script type="text/javascript">
        (function() {
            document.querySelector('#image').addEventListener('change', handleFiles);
            function handleFiles(e) {
                var image = document.querySelector('#imageHolder');
                var file = e.target.files[0];
                var reader = new FileReader();

                reader.onload=function(e) {
                    image.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        })()
    </script>
    @endpush